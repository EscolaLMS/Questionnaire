<?php

namespace EscolaLms\Questionnaire\Http\Requests;

use EscolaLms\Questionnaire\Enums\ModelEnum;
use EscolaLms\Questionnaire\Models\Questionnaire;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class QuestionnaireUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('update', Questionnaire::class);
    }

    public function rules(): array
    {
        return [
            'model' => [
                Rule::in(ModelEnum::getValues()),
            ],
            'title' => 'string',
            'model_id' => 'integer',
            'active' => 'boolean',
        ];
    }

    public function getParamModel(): string
    {
        return $this->get('model');
    }

    public function getParamTitle(): string
    {
        return $this->get('title');
    }

    public function getParamModelId(): string
    {
        return $this->get('model_id');
    }

    public function getParamActive(): string
    {
        return $this->get('active', true);
    }
}
