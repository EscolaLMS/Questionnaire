<?php

namespace EscolaLms\Questionnaire\Http\Requests;

use EscolaLms\Questionnaire\Models\Question;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class QuestionCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('create', Question::class);
    }

    public function rules(): array
    {
        return [
            'title' => 'string|required',
            'description' => 'string|required',
            'questionnaire_id' => 'integer|required',
            'position' => 'integer',
            'active' => 'boolean',
        ];
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

    public function getParamActive(): string
    {
        return $this->get('active', true);
    }
}
