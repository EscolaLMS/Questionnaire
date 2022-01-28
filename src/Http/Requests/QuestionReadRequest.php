<?php

namespace EscolaLms\Questionnaire\Http\Requests;

use EscolaLms\Questionnaire\Models\Question;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class QuestionReadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('read', Question::class);
    }

    public function rules(): array
    {
        return [];
    }
}
