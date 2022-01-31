<?php

namespace EscolaLms\Questionnaire\Database\Factories;

use EscolaLms\Questionnaire\Enums\ModelEnum;
use EscolaLms\Questionnaire\Models\Questionnaire;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuestionnaireFactory extends Factory
{
    protected $model = Questionnaire::class;

    public function definition()
    {
        return [
            'title' => $this->faker->realText,
            'model' => array_rand(ModelEnum::asSelectArray()),
            'model_id' => 1,
            'active' => true
        ];
    }
}
