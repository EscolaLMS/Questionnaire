<?php

namespace EscolaLms\Questionnaire\Http\Requests;

use EscolaLms\Questionnaire\Models\Question;
use EscolaLms\Questionnaire\Models\Questionnaire;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class QuestionUpdateRequest extends FormRequest
{
    protected function prepareForValidation()
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
        ];
    }

    public function getParamId()
    {
        return $this->route('id');
    }

    public function getParamTitle(): string
    {
        return $this->get('title');
    }

    public function getParamDescription(): string
    {
        return $this->get('description');
    }

    public function getParamQuestionnaireId(): string
    {
        return $this->get('questionnaire_id');
    }

    public function getParamPosition(): string
    {
        return $this->get('position', 1);
    }

    public function getParamActive(): bool
    {
        return $this->get('active', true);
    }

    public function getQuestion(): Question
    {
        return Question::findOrFail($this->route('id'));
    }
}
