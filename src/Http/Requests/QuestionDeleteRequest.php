<?php

namespace EscolaLms\Questionnaire\Http\Requests;

use EscolaLms\Questionnaire\Models\Question;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class QuestionDeleteRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        parent::prepareForValidation();
        $this->merge(['id' => $this->route('id')]);
    }

    public function authorize(): bool
    {
        $question = $this->getQuestion();

        return Gate::allows('delete', $question);
    }

    public function rules(): array
    {
        return [
            'id' => [
                'integer',
                'required',
                Rule::exists(Question::class, 'id'),
            ],
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
