<?php

namespace EscolaLms\Questionnaire\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QuestionnaireFrontListingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [];
    }
}
