<?php

namespace EscolaLms\Questionnaire\Http\Requests;

use EscolaLms\Questionnaire\Models\Questionnaire;
use EscolaLms\Questionnaire\Models\QuestionnaireModelType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class QuestionnaireReportRequest extends FormRequest
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
        $questionnaire = $this->getQuestionnaire();

        return Gate::allows('read', $questionnaire);
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
        /** @var int $id */
        $id = $this->route('id');
        return $id;
    }

    public function getParamModelTypeId(): ?int
    {
        /** @var int|null $id */
        $id = $this->route('model_type_id');
        return $id;
    }

    public function getParamModelId(): ?int
    {
        /** @var int|null $id */
        $id = $this->route('model_id');
        return $id;
    }

    public function getParamUserId(): ?int
    {
        /** @var int|null $id */
        $id = $this->route('user_id');
        return $id;
    }

    public function getQuestionnaire(): Questionnaire
    {
        return Questionnaire::findOrFail($this->getParamId());
    }
}
