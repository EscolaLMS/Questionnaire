<?php

namespace EscolaLms\Questionnaire\Rules;

use EscolaLms\Questionnaire\Enums\QuestionAnswersRequiredRateEnum;
use EscolaLms\Questionnaire\Models\Question;
use Illuminate\Contracts\Validation\Rule;

class QuestionAnswerRateRequired implements Rule
{
    private ?int $questionId;

    public function __construct($questionId)
    {
        $this->questionId = $questionId;
    }

    public function passes($attribute, $value): bool
    {
        if (!is_numeric($this->questionId)) {
            return false;
        }

        $question = Question::find($this->questionId);

        if ($question && in_array($question->type, QuestionAnswersRequiredRateEnum::RATE_REQUIRED) && is_null($value)) {
            return false;
        }
        return true;
    }

    public function message(): string
    {
        return 'The :attribute is required in this type of question';
    }
}
