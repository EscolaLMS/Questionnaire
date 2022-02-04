<?php

namespace EscolaLms\Questionnaire\Http\Requests;

use EscolaLms\Questionnaire\Models\Questionnaire;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class QuestionnaireCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('create', Questionnaire::class);
    }

    public function rules(): array
    {
        return [
            'title' => 'string|required',
            'active' => 'boolean',
            /*'models' => ['array'],
            'models.*' => ['sometimes', 'array'],
            'models.*.modelable_type_id' => ['integer'],
            'models.*.modelable_id' => ['integer'],*/
        ];
    }

    public function getParamTitle(): string
    {
        return $this->get('title');
    }

    public function getParamActive(): bool
    {
        return $this->get('active', true);
    }
}
