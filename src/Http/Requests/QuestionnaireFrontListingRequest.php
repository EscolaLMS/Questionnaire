<?php

namespace EscolaLms\Questionnaire\Http\Requests;

use EscolaLms\Questionnaire\Enums\QuestionTypeEnum;
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
        return true;
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
            'public_answers' => ['boolean'],
            'question_type' => ['sometimes', 'string', Rule::in(QuestionTypeEnum::getValues())],
        ];
    }

    public function getParamModelTypeTitle(): string
    {
        /** @var string $title */
        $title = $this->route('model_type_title');
        return $title;
    }

    public function getParamModelId(): int
    {
        /** @var int $id */
        $id = $this->route('model_id');
        return $id;
    }
}
