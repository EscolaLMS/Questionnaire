<?php

namespace EscolaLms\Questionnaire\Http\Requests;

use EscolaLms\Questionnaire\Models\Questionnaire;
use EscolaLms\Questionnaire\Models\QuestionnaireModelType;
use EscolaLms\Questionnaire\Rules\ClassExist;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

/**
 * @OA\Schema(
 *     schema="QuestionnaireAnswerRequest",
 *     @OA\Property(
 *          property="answers",
 *          type="array",
 *          description="answers for questionnaire"
 *     ),
 *     @OA\Property(
 *         property="answers.*.question_id",
 *         type="integer",
 *         description="question identified by id"
 *     ),
 *     @OA\Property(
 *          property="answers.*.rate",
 *          type="integer",
 *          description="rate from 1 to 5"
 *     )
 * )
 */
class QuestionnaireFrontAnswerRequest extends FormRequest
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

        return Gate::allows('readFront', $questionnaire);
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
                Rule::exists($this->getQuestionnaireModelType()->model_class, 'id'),
            ],
            'answers' => ['sometimes', 'array'],
            'answers.*' => ['sometimes', 'array'],
            'answers.*.question_id' => ['integer'],
            'answers.*.rate' => ['integer'],
        ];
    }

    public function getParamId(): int
    {
        return $this->route('id');
    }

    public function getParamModelTypeTitle(): string
    {
        return $this->route('model_type_title');
    }

    public function getParamModelId(): int
    {
        return $this->route('model_id');
    }

    public function getQuestionnaire(): Questionnaire
    {
        return Questionnaire::findOrFail($this->getParamId());
    }

    public function getQuestionnaireModelType(): QuestionnaireModelType
    {
        return QuestionnaireModelType::query()->where('title', $this->getParamModelTypeTitle())->firstOrFail();
    }
}
