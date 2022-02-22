<?php

namespace EscolaLms\Questionnaire\Http\Requests;

use EscolaLms\Questionnaire\Models\Questionnaire;
use EscolaLms\Questionnaire\Models\QuestionnaireModelType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class QuestionnaireStarsFrontRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        parent::prepareForValidation();
        $this->merge([
            'id' => $this->route('id'),
            'model_type_id' => $this->route('model_type_id'),
            'model_id' => $this->route('model_id'),
        ]);
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => [
                'integer',
                'required',
                Rule::exists(Questionnaire::class, 'id'),
            ],
            'model_type_id' => [
                'integer',
                'sometimes',
                'nullable',
                Rule::exists(QuestionnaireModelType::class, 'id'),
            ],
            'model_id' => [
                'integer',
                'sometimes',
                'nullable',
            ],
        ];
    }

    public function getParamId(): int
    {
        return $this->route('id');
    }

    public function getParamModelTypeId(): ?int
    {
        return $this->route('model_type_id');
    }

    public function getParamModelId(): ?int
    {
        return $this->route('model_id');
    }

    public function getQuestionnaire(): Questionnaire
    {
        return Questionnaire::findOrFail($this->getParamId());
    }
}
