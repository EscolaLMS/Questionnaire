<?php

namespace EscolaLms\Questionnaire\Http\Requests;

use EscolaLms\Questionnaire\Models\Question;
use EscolaLms\Questionnaire\Models\Questionnaire;
use EscolaLms\Questionnaire\Models\QuestionnaireModelType;
use EscolaLms\Questionnaire\Rules\ClassExist;
use EscolaLms\Questionnaire\Rules\ModelExist;
use EscolaLms\Questionnaire\Rules\QuestionAnswerRateRequired;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

/**
 * @OA\Schema(
 *  schema="QuestionnaireAnswerRequest",
 *  @OA\Property(
 *      property="question_id",
 *      type="integer",
 *      description="question identified by id",
 *  ),
 *  @OA\Property(
 *      property="rate",
 *      type="integer",
 *      description="rate from 1 to 5",
 *  ),
 *  @OA\Property(
 *      property="note",
 *      type="string",
 *      description="anser of text type",
 *  ),
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
                new ModelExist($this->input('model_type_title'), 'id'),
            ],
            'question_id' => ['integer', 'required', Rule::exists(Question::class, 'id')],
            'rate' => ['sometimes', 'integer', new QuestionAnswerRateRequired($this->getQuestionId())],
            'note' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function getParamId(): int
    {
        /** @var int $id */
        $id = $this->route('id');
        return $id;
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

    public function getQuestionId(): int|null
    {
        /** @var int|null $id */
        $id = $this->input('question_id');
        return $id;
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
