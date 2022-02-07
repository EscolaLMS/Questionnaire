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
            'models' => ['sometimes', 'array'],
            'models.*' => ['sometimes', 'array'],
            'models.*.model_type_id' => ['integer'],
            'models.*.model_id' => ['integer'],
        ];
    }
}
