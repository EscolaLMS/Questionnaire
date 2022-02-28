<?php

namespace EscolaLms\Questionnaire\Http\Requests;

use EscolaLms\Questionnaire\Models\Questionnaire;
use EscolaLms\Questionnaire\Models\QuestionnaireModelType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class QuestionnaireCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('create', Questionnaire::class);
    }

    public function rules(): array
    {
        return [
            'title' => ['string', 'required'],
            'active' => 'boolean',
            'models' => ['sometimes', 'array'],
            'models.*' => ['sometimes', 'array'],
            'models.*.model_type_id' => ['integer', Rule::exists(QuestionnaireModelType::class, 'id')],
            'models.*.model_id' => ['integer'],
        ];
    }
}
