<?php

namespace EscolaLms\Questionnaire\Http\Requests;

use EscolaLms\Questionnaire\Models\Question;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class QuestionReadRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        parent::prepareForValidation();
        $this->merge(['id' => $this->route('id')]);
    }

    public function authorize(): bool
    {
        $question = $this->getQuestion();

        return Gate::allows('read', $question);
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
        /** @var int $id */
        $id = $this->route('id');
        return $id;
    }

    public function getQuestion(): Question
    {
        return Question::findOrFail($this->getParamId());
    }
}
