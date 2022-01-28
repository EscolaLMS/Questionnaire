<?php

namespace EscolaLms\Questionnaire\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QuestionnaireFrontReadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [];
    }

    public function getParamSlug()
    {
        return $this->route('id');
    }
}
