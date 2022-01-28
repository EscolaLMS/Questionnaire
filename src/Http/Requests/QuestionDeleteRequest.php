<?php

namespace EscolaLms\Questionnaire\Http\Requests;

use EscolaLms\Questionnaire\Models\Question;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class QuestionDeleteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('delete', Question::class);
    }

    public function rules(): array
    {
        return [];
    }
}
