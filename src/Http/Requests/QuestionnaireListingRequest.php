<?php

namespace EscolaLms\Questionnaire\Http\Requests;

use EscolaLms\Questionnaire\Models\Questionnaire;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class QuestionnaireListingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('list', Questionnaire::class);
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string'],
            'order_by' => ['sometimes', 'string', 'in:id,title,created_at'],
            'order' => ['sometimes', 'string', 'in:ASC,DESC'],
        ];
    }
}
