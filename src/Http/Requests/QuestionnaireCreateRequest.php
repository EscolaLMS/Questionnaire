<?php

namespace EscolaLms\Questionnaire\Http\Requests;

use EscolaLms\Questionnaire\Enums\ModelEnum;
use EscolaLms\Questionnaire\Models\Questionnaire;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class QuestionnaireCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('create', Questionnaire::class);
    }

    public function rules(): array
    {
        return [
            /*'model' => [
                'required',
                Rule::in(ModelEnum::getValues()),
            ],*/
            'title' => 'string|required',
            //'model_id' => 'integer|required',
            'active' => 'boolean',
        ];
    }

    /*public function getParamModel(): string
    {
        return $this->get('model');
    }*/

    public function getParamTitle(): string
    {
        return $this->get('title');
    }

    /*public function getParamModelId(): string
    {
        return $this->get('model_id');
    }*/

    public function getParamActive(): bool
    {
        return $this->get('active', true);
    }
}
