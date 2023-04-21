<?php

namespace EscolaLms\Questionnaire\Http\Requests;

use EscolaLms\Questionnaire\Enums\QuestionTypeEnum;
use EscolaLms\Questionnaire\Models\Question;
use EscolaLms\Questionnaire\Models\Questionnaire;
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
            'type' => ['string', Rule::in(QuestionTypeEnum::getValues())],
            'public_answers' => 'boolean',
        ];
    }

    public function getParamId(): int
    {
        return $this->route('id');
    }

    public function getQuestion(): Question
    {
        return Question::findOrFail($this->getParamId());
    }
}
