<?php

namespace EscolaLms\Questionnaire\Database\Factories;

use EscolaLms\Questionnaire\Enums\ModelEnum;
use EscolaLms\Questionnaire\Models\Questionnaire;
use EscolaLms\Questionnaire\Models\QuestionnaireModel;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuestionnaireModelFactory extends Factory
{
    protected $model = QuestionnaireModel::class;

    public function definition()
    {
        /** @var Questionnaire $questionnaire */
        $questionnaire = Questionnaire::query()->inRandomOrder()->first();
        if (empty($questionnaire)) {
            Questionnaire::factory()
                ->count(1)
                ->create();
            $questionnaire = Questionnaire::query()->inRandomOrder()->first();
        }

        return [
            'questionnaire_id' => $questionnaire->id,
            'modelable_type' => array_rand(ModelEnum::asSelectArray()),
            'modelable_id' => 1,
        ];
    }
}
