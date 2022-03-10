<?php

namespace EscolaLms\Questionnaire\Http\Requests;

use EscolaLms\Questionnaire\Models\Question;
use EscolaLms\Questionnaire\Models\Questionnaire;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class QuestionAnswerListingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('list', Question::class);
    }

    protected function prepareForValidation(): void
    {
        parent::prepareForValidation();
        $this->merge([
            'questionnaire_id' => $this->route('id'),
        ]);
    }

    public function rules(): array
    {
        return [
            'questionnaire_id' => [
                'integer',
                'required',
                Rule::exists(Questionnaire::class, 'id'),
            ],
            'question_id' => [
                'integer',
                'sometimes',
                'nullable',
            ],
            'questionnaire_model_id' => [
                'integer',
                'sometimes',
                'nullable',
            ],
        ];
    }
}
