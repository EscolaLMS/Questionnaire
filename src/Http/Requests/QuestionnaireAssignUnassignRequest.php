<?php

namespace EscolaLms\Questionnaire\Http\Requests;

use EscolaLms\Questionnaire\Models\Questionnaire;
use EscolaLms\Questionnaire\Models\QuestionnaireModelType;
use EscolaLms\Questionnaire\Rules\ClassExist;
use EscolaLms\Questionnaire\Rules\ModelExist;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class QuestionnaireAssignUnassignRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        parent::prepareForValidation();
        $this->merge([
            'id' => $this->route('id'),
            'model_type_title' => $this->route('model_type_title'),
            'model_id' => $this->route('model_id')
        ]);
    }

    public function authorize(): bool
    {
        $questionnaire = $this->getQuestionnaire();

        return Gate::allows('update', $questionnaire);
    }

    public function rules(): array
    {
        return [
            'id' => [
                'integer',
                'required',
                Rule::exists(Questionnaire::class, 'id'),
            ],
            'model_type_title' => [
                'string',
                Rule::exists(QuestionnaireModelType::class, 'title'),
                new ClassExist
            ],
            'model_id' => [
                'integer',
                new ModelExist($this->input('model_type_title'), 'id'),
            ],
        ];
    }

    public function getParamId(): int
    {
        return $this->route('id');
    }

    public function getModelTypeTitle(): string
    {
        return $this->route('model_type_title');
    }

    public function getModelId(): int
    {
        return $this->route('model_id');
    }

    public function getQuestionnaire(): Questionnaire
    {
        return Questionnaire::findOrFail($this->getParamId());
    }

    public function getQuestionnaireModelType(): QuestionnaireModelType
    {
        return QuestionnaireModelType::query()->where('title', $this->getModelTypeTitle())->firstOrFail();
    }
}
