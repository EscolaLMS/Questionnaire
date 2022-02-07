<?php

namespace EscolaLms\Questionnaire\Http\Requests;

use EscolaLms\Questionnaire\Models\Questionnaire;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class QuestionnaireUpdateRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        parent::prepareForValidation();
        $this->merge(['id' => $this->route('id')]);
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
            'title' => 'string',
            'active' => 'boolean',
            'models' => ['sometimes', 'array'],
            'models.*' => ['sometimes', 'array'],
            'models.*.model_type_id' => ['integer'],
            'models.*.model_id' => ['integer'],
        ];
    }

    public function getParamId(): int
    {
        return $this->route('id');
    }

    public function getQuestionnaire(): Questionnaire
    {
        return Questionnaire::findOrFail($this->getParamId());
    }
}
