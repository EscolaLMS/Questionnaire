<?php

namespace EscolaLms\Questionnaire\Database\Factories;

use EscolaLms\Questionnaire\Models\QuestionnaireModelType;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuestionnaireModelTypeFactory extends Factory
{
    protected $model = QuestionnaireModelType::class;

    public function definition()
    {
        return [
            'title' => 'Course',
            'modelable_class' => 'EscolaLms\Courses\Models\Course',
        ];
    }
}
