<?php

namespace EscolaLms\Questionnaire\Tests\Api;

use EscolaLms\Questionnaire\Database\Seeders\QuestionnairePermissionsSeeder;
use EscolaLms\Questionnaire\Enums\QuestionnaireRateMap;
use EscolaLms\Questionnaire\Enums\QuestionTypeEnum;
use EscolaLms\Questionnaire\Models\Question;
use EscolaLms\Questionnaire\Models\QuestionAnswer;
use EscolaLms\Questionnaire\Models\Questionnaire;
use EscolaLms\Questionnaire\Models\QuestionnaireModel;
use EscolaLms\Questionnaire\Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Testing\Fluent\AssertableJson;

class QuestionnaireStarsFrontTest extends TestCase
{
    use DatabaseTransactions;

    public Questionnaire $questionnaire;
    public QuestionnaireModel $questionnaireModel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(QuestionnairePermissionsSeeder::class);
        $this->authenticateAsAdmin();

        $this->questionnaire = Questionnaire::factory()->createOne();
        $this->questionnaireModel = QuestionnaireModel::factory()->createOne();

        Question::factory()
            ->count(20)
            ->create();

        QuestionAnswer::factory()
            ->count(200)
            ->create();
    }

    public function testCanReadQuestionnaireStars(): void
    {
        $response = $this->actingAs($this->user, 'api')->getJson(
            sprintf(
                '/api/questionnaire/stars/%s/%d',
                $this->questionnaireModel->modelableType->title,
                $this->questionnaireModel->model_id
            )
        );
        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'data',
            'message'
        ]);
        $response->assertJson(fn (AssertableJson $json) =>
            $json->has('data', fn (AssertableJson $json) =>
                $json
                    ->has('sum_rates')
                    ->has('count_answers')
                    ->has('avg_rate')
                    ->has('rates')->etc()
            )->etc()
        );
    }

    public function testReadModelReviewStars(): void
    {
        $questionnaire = Questionnaire::factory()->createOne();
        $questionnaireModel = QuestionnaireModel::factory([
            'questionnaire_id' => $questionnaire->getKey(),
        ])->createOne();
        $question = Question::factory([
            'questionnaire_id' => $questionnaire->getKey(),
            'public_answers' => true,
            'type' => QuestionTypeEnum::REVIEW,
        ])->createOne();
        QuestionAnswer::factory([
            'questionnaire_model_id' => $questionnaireModel->getKey(),
            'question_id' => $question->getKey(),
            'visible_on_front' => true,
        ])
            ->count(5)
            ->sequence(fn ($sequence) => ['rate' => $sequence->index])
            ->create();
        QuestionAnswer::factory([
            'questionnaire_model_id' => $questionnaireModel->getKey(),
            'question_id' => $question->getKey(),
            'visible_on_front' => false,
        ])
            ->count(5)
            ->sequence(fn ($sequence) => ['rate' => $sequence->index])
            ->create();

        $this
            ->actingAs($this->user)
            ->json(
                'GET',
                '/api/questionnaire/' . $questionnaireModel->modelableType->title . '/' . $questionnaireModel->model_id . '/questions/' . $question->getKey() . '/stars',
            )
            ->assertOk()
            ->assertJson(['data' => [
                'avg_rate' => 2.0,
                'count_answers' => 10,
                'question_id' => $question->getKey(),
                'count_public_answers' => 5,
            ]]);
    }
}
