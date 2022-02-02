<?php

namespace EscolaLms\Questionnaire\Database\Factories;

use Database\Factories\EscolaLms\Core\Models\UserFactory;
use EscolaLms\Core\Models\User;
use EscolaLms\Questionnaire\Models\Question;
use EscolaLms\Questionnaire\Models\QuestionAnswer;
use EscolaLms\Questionnaire\Models\QuestionnaireModel;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuestionAnswerFactory extends Factory
{
    protected $model = QuestionAnswer::class;

    public function definition()
    {
        /** @var Question $question */
        $question = Question::query()->inRandomOrder()->first();
        if (empty($question)) {
            QuestionFactory::factory()
                ->count(1)
                ->create();
            $question = QuestionFactory::query()->inRandomOrder()->first();
        }

        /** @var QuestionnaireModel $questionnaireModel */
        $questionnaireModel = QuestionnaireModel::query()->inRandomOrder()->first();
        if (empty($questionnaireModel)) {
            QuestionnaireModelFactory::factory()
                ->count(1)
                ->create();
            $questionnaireModel = QuestionnaireModelFactory::query()->inRandomOrder()->first();
        }

        /** @var User $user */
        $user = User::query()->inRandomOrder()->first();
        if (empty($user)) {
            $user = UserFactory::new();
        }

        return [
            'questionnaire_model_id' => $questionnaireModel->id,
            'question_id' => $question->id,
            'user_id' => $user->id,
            'rate' => $this->faker->numberBetween(1, 5),
        ];
    }
}
