<?php

namespace EscolaLms\Questionnaire\Http\Requests;

use EscolaLms\Questionnaire\Enums\QuestionTypeEnum;
use EscolaLms\Questionnaire\Models\Question;
use EscolaLms\Questionnaire\Models\Questionnaire;
use EscolaLms\Questionnaire\Rules\LimitQuestionReview;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class QuestionCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('create', Question::class);
    }

    public function rules(): array
    {
        return [
            'title' => 'string|required',
            'description' => 'string|required',
            'questionnaire_id' => [
                'integer',
                'required',
                Rule::exists(Questionnaire::class, 'id'),
            ],
            'position' => 'required|integer',
            'active' => 'boolean',
            'type' => [
                'string',
                Rule::in(QuestionTypeEnum::getValues()),
                new LimitQuestionReview($this->input('questionnaire_id'))
            ],
            'public_answers' => 'boolean',
        ];
    }
}
