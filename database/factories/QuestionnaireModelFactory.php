<?php

namespace EscolaLms\Questionnaire\Database\Factories;

use EscolaLms\Questionnaire\Models\Questionnaire;
use EscolaLms\Questionnaire\Models\QuestionnaireModel;
use EscolaLms\Questionnaire\Models\QuestionnaireModelType;
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

        /** @var QuestionnaireModelType $questionnaireModelType */
        $questionnaireModelType = QuestionnaireModelType::query()->inRandomOrder()->first();
        if (empty($questionnaireModelType)) {
            QuestionnaireModelType::factory()
                ->count(1)
                ->create();
            $questionnaireModelType = QuestionnaireModelType::query()->inRandomOrder()->first();
        }

        $model = new $questionnaireModelType->model_class();
        $newModel = $model::query()->inRandomOrder()->first();
        if (empty($newModel)) {
            $model::factory()
                ->count(1)
                ->create();
            $newModel = $model::query()->inRandomOrder()->first();
        }

        return [
            'questionnaire_id' => $questionnaire->id,
            'model_type_id' => $questionnaireModelType->id,
            'model_id' => $newModel->id,
        ];
    }
}
