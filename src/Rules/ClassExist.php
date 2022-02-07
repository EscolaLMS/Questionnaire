<?php

namespace EscolaLms\Questionnaire\Rules;

use EscolaLms\Questionnaire\Models\QuestionnaireModelType;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Database\Eloquent\Model;

class ClassExist implements Rule
{
    public function passes($attribute, $value)
    {
        try {
            $questionnaireModelType = QuestionnaireModelType::query()->where('title', $value)->firstOrFail();
        } catch (\Exception $exception) {
            return false;
        }

        return is_a($questionnaireModelType->model_class, Model::class, true);
    }

    public function message()
    {
        return 'The :attribute got wrong class name';
    }
}
