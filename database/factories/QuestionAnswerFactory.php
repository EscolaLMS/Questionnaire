<?php

namespace EscolaLms\Questionnaire\Database\Factories;

use Database\Factories\EscolaLms\Core\Models\UserFactory;
use EscolaLms\Core\Models\User;
use EscolaLms\Questionnaire\Models\Question;
use EscolaLms\Questionnaire\Models\QuestionAnswer;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuestionAnswerFactory extends Factory
{
    protected $model = QuestionAnswer::class;

    public function definition()
    {
        /** @var Question $question */
        $question = Question::query()->inRandomOrder()->first();
        if (empty($question)) {
            $question = QuestionFactory::new();
        }

        /** @var User $user */
        $user = User::query()->inRandomOrder()->first();
        if (empty($question)) {
            $user = UserFactory::new();
        }

        return [
            'question_id' => $question->id,
            'user_id' => $user->id,
            'rate' => $this->faker->numberBetween(1, 5),
        ];
    }
}
