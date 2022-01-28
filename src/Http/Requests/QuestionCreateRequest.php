<?php

namespace EscolaLms\Questionnaire\Http\Requests;

use EscolaLms\Questionnaire\Models\Question;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class QuestionCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('create', Question::class);
    }

    public function rules(): array
    {
        return [
            'slug' => 'string|required|unique:pages',
            'title' => 'string|required',
            'content' => 'string|required',
        ];
    }

    public function getParamTitle(): string
    {
        return $this->get('title');
    }

    public function getParamSlug(): string
    {
        return $this->get('slug');
    }

    public function getParamContent(): string
    {
        return $this->get('content', '');
    }
}
