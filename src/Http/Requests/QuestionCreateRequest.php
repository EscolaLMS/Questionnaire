<?php

namespace EscolaLms\Questionnaire\Http\Requests;

use EscolaLms\Questionnaire\Models\Question;
use EscolaLms\Questionnaire\Models\Questionnaire;
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
            'position' => 'integer',
            'active' => 'boolean',
        ];
    }
}
