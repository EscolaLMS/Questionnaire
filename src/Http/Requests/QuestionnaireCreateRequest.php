<?php

namespace EscolaLms\Questionnaire\Http\Requests;

use EscolaLms\Questionnaire\Models\Questionnaire;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class QuestionnaireCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('create', Questionnaire::class);
    }

    public function rules(): array
    {
        return [
            'title' => 'string|required',
            'active' => 'boolean',
        ];
    }

    public function getParamTitle(): string
    {
        return $this->get('title');
    }

    public function getParamActive(): bool
    {
        return $this->get('active', true);
    }
}
