<?php

namespace EscolaLms\Questionnaire\Rules;

use EscolaLms\Questionnaire\Enums\QuestionTypeEnum;
use EscolaLms\Questionnaire\Models\Question;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Database\Eloquent\Builder;

class LimitQuestionReview implements Rule
{
    private ?int $questionnaireId;
    private ?int $questionId;

    public function __construct($questionnaireId = null, $questionId = null)
    {
        $this->questionnaireId = $questionnaireId;
        $this->questionId = $questionId;
    }

    public function passes($attribute, $value): bool
    {
        if ($this->questionnaireId && $value === QuestionTypeEnum::REVIEW) {
            return !Question::whereHas('questionnaire', fn (Builder $query) => $query->where('questionnaire_id', '=', $this->questionnaireId))
                ->where('type', '=', QuestionTypeEnum::REVIEW)
                ->where('id', '!=', $this->questionId)->exists();
        }

        return true;
    }

    public function message()
    {
        return 'A questionnaire can only have one review question';
    }
}
