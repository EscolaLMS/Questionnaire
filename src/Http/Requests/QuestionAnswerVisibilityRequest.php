<?php

namespace EscolaLms\Questionnaire\Http\Requests;

use EscolaLms\Questionnaire\Models\Question;
use EscolaLms\Questionnaire\Models\QuestionAnswer;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

/**
 * @OA\Schema(
 *  schema="QuestionnaireAnswerChangeVisibility",
 *  @OA\Property(
 *      property="visible_on_front",
 *      type="boolean",
 *      description="visible on front",
 *  )
 * )
 */
class QuestionAnswerVisibilityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('changeVisibility', $this->getQuestionAnswer());
    }

    public function rules(): array
    {
        return [
            'visible_on_front' => ['required', 'boolean'],
        ];
    }

    public function getQuestionAnswerId(): ?int
    {
        /** @var int|null $id */
        $id = $this->route('id');
        return $id;
    }

    public function getQuestionAnswer(): QuestionAnswer
    {
        return QuestionAnswer::findOrFail($this->getQuestionAnswerId());
    }
}
