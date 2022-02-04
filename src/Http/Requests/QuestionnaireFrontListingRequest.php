<?php

namespace EscolaLms\Questionnaire\Http\Requests;

use EscolaLms\Questionnaire\Models\Questionnaire;
use EscolaLms\Questionnaire\Models\QuestionnaireModelType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class QuestionnaireFrontListingRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        parent::prepareForValidation();
        $this->merge([
            'model_type_title' => $this->route('model_type_title'),
            'model_id' => $this->route('model_id'),
        ]);
    }

    public function authorize(): bool
    {
        return Gate::allows('listFront', Questionnaire::class);
    }

    public function rules(): array
    {
        return [
            'model_type_title' => [
                'string',
                Rule::exists(QuestionnaireModelType::class, 'title'),
            ],
            'model_id' => [
                'integer',
            ],
        ];
    }

    public function getParamModelTypeTitle(): string
    {
        return $this->route('model_type_title');
    }

    public function getParamModelId(): int
    {
        return $this->route('model_id');
    }
}
