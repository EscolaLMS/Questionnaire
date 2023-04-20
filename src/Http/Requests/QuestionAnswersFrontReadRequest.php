<?php

namespace EscolaLms\Questionnaire\Http\Requests;

use EscolaLms\Questionnaire\Models\Question;
use EscolaLms\Questionnaire\Models\QuestionnaireModelType;
use EscolaLms\Questionnaire\Rules\ClassExist;
use EscolaLms\Questionnaire\Rules\ModelExist;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class QuestionAnswersFrontReadRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        parent::prepareForValidation();
        $this->merge([
            'question_id' => $this->route('question_id'),
            'model_type_title' => $this->route('model_type_title'),
            'model_id' => $this->route('model_id'),
        ]);
    }

    public function authorize(): bool
    {
        $question = $this->getQuestion();

        return Gate::allows('readAnswers', $question);
    }

    public function rules(): array
    {
        return [
            'question_id' => [
                'integer',
                'required',
                Rule::exists(Question::class, 'id'),
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
        return $this->route('question_id');
    }

    public function getParamModelTypeTitle(): string
    {
        return $this->route('model_type_title');
    }

    public function getParamModelId(): int
    {
        return $this->route('model_id');
    }

    public function getQuestion(): Question
    {
        return Question::findOrFail($this->getParamId());
    }

    public function getQuestionnaireModelType(): QuestionnaireModelType
    {
        return QuestionnaireModelType::query()->where('title', $this->getParamModelTypeTitle())->firstOrFail();
    }
}
