<?php

namespace EscolaLms\Questionnaire\Tests\Api;

use EscolaLms\Questionnaire\Database\Seeders\QuestionnairePermissionsSeeder;
use EscolaLms\Questionnaire\Enums\QuestionTypeEnum;
use EscolaLms\Questionnaire\EscolaLmsQuestionnaireServiceProvider;
use EscolaLms\Questionnaire\Models\Question;
use EscolaLms\Questionnaire\Models\QuestionAnswer;
use EscolaLms\Questionnaire\Models\Questionnaire;
use EscolaLms\Questionnaire\Models\QuestionnaireModel;
use EscolaLms\Questionnaire\Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Config;

class QuestionAnswerTest extends TestCase
{
    use DatabaseTransactions;

    public Questionnaire $questionnaire;
    public QuestionnaireModel $questionnaireModel;
    public Question $question;
    public Question $questionText;
    public Question $questionReview;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(QuestionnairePermissionsSeeder::class);
        $this->authenticateAsAdmin();

        $this->questionnaire = Questionnaire::factory()->createOne();
        $this->questionnaireModel = QuestionnaireModel::factory([
            'questionnaire_id' => $this->questionnaire->getKey(),
        ])->createOne();

        $this->question = Question::factory([
            'questionnaire_id' => $this->questionnaire->getKey(),
            'type' => QuestionTypeEnum::RATE,
        ])->createOne();

        $this->questionText = Question::factory([
                'questionnaire_id' => $this->questionnaire->getKey(),
                'type' => QuestionTypeEnum::TEXT,
        ])->createOne();

        $this->questionReview = Question::factory([
            'questionnaire_id' => $this->questionnaire->getKey(),
            'type' => QuestionTypeEnum::REVIEW,
        ])->createOne();
    }

    private function uri(int $id, string $modelTypeTitle, int $modelId): string
    {
        return sprintf('/api/questionnaire/%s/%d/%d', $modelTypeTitle, $modelId, $id);
    }

    public function testAdminCanAnswerQuestions(): void
    {
        $response = $this->actingAs($this->user, 'api')->postJson(
            $this->uri(
                $this->questionnaire->id,
                $this->questionnaireModel->modelableType->title,
                $this->questionnaireModel->model_id
            ),
            [
                'question_id' => $this->question->getKey(),
                'rate' => 5,
            ]
        )
            ->assertOk()
            ->assertJsonFragment([
                'rate' => 5,
            ]);

        $data = json_decode($response->getContent());
        $this->assertEquals($data->data->id, $this->questionnaire->id);

        $response = $this->actingAs($this->user, 'api')->postJson(
            $this->uri(
                $this->questionnaire->id,
                $this->questionnaireModel->modelableType->title,
                $this->questionnaireModel->model_id
            ),
            [
                'question_id' => $this->questionText->getKey(),
                'note' => 'test_1'
            ]
        )
            ->assertOk()
            ->assertJsonFragment([
                'note' => 'test_1',
            ]);
        $data = json_decode($response->getContent());
        $this->assertEquals($data->data->id, $this->questionnaire->id);

        $response = $this->actingAs($this->user, 'api')->postJson(
            $this->uri(
                $this->questionnaire->id,
                $this->questionnaireModel->modelableType->title,
                $this->questionnaireModel->model_id
            ),
            [
                'question_id' => $this->questionReview->getKey(),
                'note' => 'test_2',
                'rate' => 5,
            ]
        )
            ->assertOk()
            ->assertJsonFragment([
                'note' => 'test_2',
                'rate' => 5,
            ]);
        $data = json_decode($response->getContent());
        $this->assertEquals($data->data->id, $this->questionnaire->id);
    }

    public function testAdminCannotAnswerMissingQuestion(): void
    {
        $response = $this->actingAs($this->user, 'api')->postJson(
            $this->uri(
                999999,
                $this->questionnaireModel->modelableType->title,
                $this->questionnaireModel->model_id
            ),
            [
                'question_id' => $this->question->getKey(),
                'rate' => 5,
            ]
        );

        $response->assertStatus(404);
        $this->assertEquals(0, QuestionAnswer::count());
    }

    public function testGuestCannotAnswerQuestion(): void
    {
        $response = $this->postJson(
            $this->uri(
                $this->questionnaire->id,
                $this->questionnaireModel->modelableType->title,
                $this->questionnaireModel->model_id
            ),
            [
                'question_id' => $this->question->getKey(),
                'rate' => 5,
            ]
        );

        $response->assertForbidden();
        $this->assertEquals(0, QuestionAnswer::count());
    }

    public function testAdminCanGetAnswerList(): void
    {
        QuestionAnswer::factory([
            'questionnaire_model_id' => $this->questionnaireModel->getKey(),
            'question_id' => $this->question->getKey(),
        ])
            ->count(20)
            ->create();

        $response = $this->actingAs($this->user, 'api')->getJson(
            '/api/admin/question-answers/' . $this->questionnaire->id
        );

        $response->assertOk();

        $data = json_decode($response->getContent());

        $this->assertEquals(20, $data->meta->total);
    }

    public function testAdminCanGetAnswerListWithFiltersOk(): void
    {
        QuestionAnswer::factory()
            ->count(20)
            ->create();
        $questionnaireModel = QuestionnaireModel::factory()->createOne();
        QuestionAnswer::factory()
            ->count(10)
            ->create([
                'questionnaire_model_id' => $questionnaireModel->id
            ]);

        $response = $this->actingAs($this->user, 'api')->getJson(
            '/api/admin/question-answers/' .
            $this->questionnaire->id .
            '?questionnaire_model_id=' . $questionnaireModel->id
        );

        $response->assertOk();

        $data = json_decode($response->getContent());

        $this->assertEquals(10, $data->meta->total);
    }

    public function testAdminCanGetAnswerListWithWrongFilters(): void
    {
        QuestionAnswer::factory()
            ->count(20)
            ->create();

        $response = $this->actingAs($this->user, 'api')->getJson(
            '/api/admin/question-answers/' .
            $this->questionnaire->id . '?question_id=' . 999999 .
            '&questionnaire_model_id=' . 9999999
        );

        $response->assertOk();

        $data = json_decode($response->getContent());

        $this->assertEquals(0, $data->meta->total);
    }
    public function testQuestionAnswersNotPublic(): void
    {
        $this->question->public_answers = false;
        $this->question->save();
        QuestionAnswer::factory([
            'questionnaire_model_id' => $this->questionnaireModel->getKey(),
            'question_id' => $this->question->getKey(),
        ])
            ->count(20)
            ->create();

        $this
            ->actingAs($this->user)
            ->json('GET', '/api/questionnaire/' . $this->questionnaireModel->modelableType->title . '/' . $this->questionnaireModel->model_id . '/questions/' . $this->question->getKey() . '/answers')
            ->assertForbidden();
    }

    public function testQuestionAnswersPublic(): void
    {
        $this->question->public_answers = true;
        $this->question->save();
        QuestionAnswer::factory()
            ->count(10)
            ->create([
                'questionnaire_model_id' => $this->questionnaireModel->getKey(),
                'question_id' => $this->question->getKey(),
                'visible_on_front' => true,
                'note' => 'ok',
            ]);

        QuestionAnswer::factory()
            ->count(10)
            ->create([
                'questionnaire_model_id' => $this->questionnaireModel->getKey(),
                'question_id' => $this->question->getKey(),
                'visible_on_front' => false,
                'note' => 'ok',
            ]);

        $this
            ->actingAs($this->user)
            ->json('GET', '/api/questionnaire/' . $this->questionnaireModel->modelableType->title . '/' . $this->questionnaireModel->model_id . '/questions/' . $this->question->getKey() . '/answers')
            ->assertOk()
            ->assertJsonCount(10, 'data');
    }

    public function publicAnswersProvider(): array
    {
        return [
            'notPublic' => [false],
            'public' => [true],
        ];
    }

    /**
     * @dataProvider publicAnswersProvider
     */
    public function testAddQuestionAnswersDefaultFalse(bool $public): void
    {
        $this->question->public_answers = $public;
        $this->question->save();

        Config::set(EscolaLmsQuestionnaireServiceProvider::CONFIG_KEY . '.new_answers_visible_by_default', false);

        $this
            ->actingAs($this->user)
            ->json(
                'POST',
                '/api/questionnaire/' . $this->questionnaireModel->modelableType->title . '/' . $this->questionnaireModel->model_id . '/' . $this->questionnaire->getKey(),
                [
                    'question_id' => $this->question->getKey(),
                    'rate' => 5,
                    'note' => 'Lorem ipsum',
                ]
            )
            ->assertOk();

        $this->assertDatabaseHas('question_answers', [
            'user_id' => $this->user->getKey(),
            'question_id' => $this->question->getKey(),
            'questionnaire_model_id' => $this->questionnaireModel->getKey(),
            'rate' => 5,
            'note' => 'Lorem ipsum',
            'visible_on_front' => false,
        ]);
    }

    /**
     * @dataProvider publicAnswersProvider
     */
    public function testAddQuestionAnswersDefaultTrue(bool $public): void
    {
        $this->question->public_answers = $public;
        $this->question->save();

        Config::set(EscolaLmsQuestionnaireServiceProvider::CONFIG_KEY . '.new_answers_visible_by_default', true);

        $this
            ->actingAs($this->user)
            ->json(
                'POST',
                '/api/questionnaire/' . $this->questionnaireModel->modelableType->title . '/' . $this->questionnaireModel->model_id . '/' . $this->questionnaire->getKey(),
                [
                    'question_id' => $this->question->getKey(),
                    'rate' => 4,
                    'note' => 'Another opinion',
                ]
            )
            ->assertOk();

        $this->assertDatabaseHas('question_answers', [
            'user_id' => $this->user->getKey(),
            'question_id' => $this->question->getKey(),
            'questionnaire_model_id' => $this->questionnaireModel->getKey(),
            'rate' => 4,
            'note' => 'Another opinion',
            'visible_on_front' => $public,
        ]);
    }

    public function testAnswerQuestionsValidateRate(): void
    {
        $this->actingAs($this->user, 'api')->postJson(
            $this->uri(
                $this->questionnaire->id,
                $this->questionnaireModel->modelableType->title,
                $this->questionnaireModel->model_id
            ),
            [
                'question_id' => $this->question->getKey(),
                'note' => 'test',
                'rate' => null,
            ]
        )
            ->assertUnprocessable()
            ->assertJsonFragment([
                'The rate is required in this type of question'
            ]);

        $this->actingAs($this->user, 'api')->postJson(
            $this->uri(
                $this->questionnaire->id,
                $this->questionnaireModel->modelableType->title,
                $this->questionnaireModel->model_id
            ),
            [
                'question_id' => $this->questionReview->getKey(),
                'note' => 'test_2',
                'rate' => null,
            ]
        )
            ->assertUnprocessable()
            ->assertJsonFragment([
                'The rate is required in this type of question'
            ]);
    }

    public function testChangeAnswerVisibilityUnauthorized(): void
    {
        $answer = QuestionAnswer::factory()->create([
            'questionnaire_model_id' => $this->questionnaireModel->getKey(),
            'question_id' => $this->questionReview->getKey(),
            'visible_on_front' => true,
        ]);

        $this
            ->json('POST', "/api/admin/question-answers/{$answer->getKey()}/change-visibility", ['visible_on_front' => false])
            ->assertUnauthorized();
    }

    public function testAdminCanChangeAnswerVisibility(): void
    {
        $answer = QuestionAnswer::factory()->create([
            'questionnaire_model_id' => $this->questionnaireModel->getKey(),
            'question_id' => $this->questionReview->getKey(),
            'visible_on_front' => true,
        ]);

        $this
            ->actingAs($this->user, 'api')
            ->json('POST', "/api/admin/question-answers/{$answer->getKey()}/change-visibility", ['visible_on_front' => false])
            ->assertOk()
            ->assertJsonFragment([
                'id' => $answer->getKey(),
                'questionnaire_model_id' => $this->questionnaireModel->getKey(),
                'question_id' => $this->questionReview->getKey(),
                'visible_on_front' => false,
            ]);

        $this->assertDatabaseHas('question_answers', [
            'id' => $answer->getKey(),
            'questionnaire_model_id' => $this->questionnaireModel->getKey(),
            'question_id' => $this->questionReview->getKey(),
            'visible_on_front' => false,
        ]);

        $this
            ->actingAs($this->user, 'api')
            ->json('POST', "/api/admin/question-answers/{$answer->getKey()}/change-visibility", ['visible_on_front' => true])
            ->assertOk()
            ->assertJsonFragment([
                'id' => $answer->getKey(),
                'questionnaire_model_id' => $this->questionnaireModel->getKey(),
                'question_id' => $this->questionReview->getKey(),
                'visible_on_front' => true,
            ]);

        $this->assertDatabaseHas('question_answers', [
            'id' => $answer->getKey(),
            'questionnaire_model_id' => $this->questionnaireModel->getKey(),
            'question_id' => $this->questionReview->getKey(),
            'visible_on_front' => true,
        ]);
    }
}
