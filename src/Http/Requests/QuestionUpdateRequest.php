<?php

namespace EscolaLms\Questionnaire\Http\Requests;

use EscolaLms\Questionnaire\Enums\QuestionTypeEnum;
use EscolaLms\Questionnaire\Models\Question;
use EscolaLms\Questionnaire\Models\Questionnaire;
use EscolaLms\Questionnaire\Rules\LimitQuestionReview;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class QuestionUpdateRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        parent::prepareForValidation();
        $this->merge(['id' => $this->route('id')]);
    }

    public function authorize(): bool
    {
        $question = $this->getQuestion();

        return Gate::allows('update', $question);
    }

    public function rules(): array
    {
        return [
            'id' => [
                'integer',
                'required',
                Rule::exists(Question::class, 'id'),
            ],
            'title' => 'string',
            'description' => 'string',
            'questionnaire_id' => [
                'integer',
                'required',
                Rule::exists(Questionnaire::class, 'id'),
            ],
            'position' => 'integer',
            'active' => 'boolean',
            'type' => [
                'string',
                Rule::in(QuestionTypeEnum::getValues()),
                new LimitQuestionReview($this->input('questionnaire_id'), $this->input('id'))
            ],
            'public_answers' => 'boolean',
            'max_score' => 'integer',
        ];
    }

    public function getParamId(): int
    {
        /** @var int $id */
        $id = $this->route('id');
        return $id;
    }

    public function getQuestion(): Question
    {
        return Question::findOrFail($this->getParamId());
    }
}
