<?php

namespace EscolaLms\Questionnaire\Http\Requests;

use EscolaLms\Questionnaire\Models\QuestionnaireModelType;
use EscolaLms\Questionnaire\Rules\ClassExist;
use EscolaLms\Questionnaire\Rules\ModelExist;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class QuestionnaireStarsFrontRequest extends FormRequest
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
        return true;
    }

    public function rules(): array
    {
        return [
            'model_type_title' => [
                'string',
                Rule::exists(QuestionnaireModelType::class, 'title'),
                new ClassExist
            ],
            'model_id' => [
                'integer',
                new ModelExist($this->getQuestionnaireModelType()->model_class, 'id'),
            ],
        ];
    }

    public function getParamModelTypeTitle(): string
    {
        return $this->route('model_type_title');
    }

    public function getParamModelId(): ?int
    {
        return $this->route('model_id');
    }

    public function getQuestionnaireModelType(): QuestionnaireModelType
    {
        return QuestionnaireModelType::query()->where('title', $this->getParamModelTypeTitle())->firstOrFail();
    }
}
