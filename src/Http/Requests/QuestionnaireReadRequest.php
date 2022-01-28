<?php

namespace EscolaLms\Questionnaire\Http\Requests;

use EscolaLms\Questionnaire\Models\Questionnaire;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class QuestionnaireReadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('read', Questionnaire::class);
    }

    public function rules(): array
    {
        return [];
    }
}
