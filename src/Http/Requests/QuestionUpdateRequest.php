<?php

namespace EscolaLms\Questionnaire\Http\Requests;

use EscolaLms\Questionnaire\Models\Question;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class QuestionUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('update', Question::class);
    }

    public function rules(): array
    {
        return [
            'slug' => ['string', Rule::unique('pages')->ignore($this->route('id'))],
            'title' => ['string'],
            'content' => ['string'],
        ];
    }

    public function getParamSlug(): string
    {
        return $this->get('slug');
    }

    public function getParamTitle(): string
    {
        return $this->get('title');
    }

    public function getParamContent(): string
    {
        return $this->get('content');
    }
}
