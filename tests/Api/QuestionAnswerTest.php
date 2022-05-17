<?php

namespace EscolaLms\Questionnaire\Tests\Api;

use EscolaLms\Questionnaire\Database\Seeders\QuestionnairePermissionsSeeder;
use EscolaLms\Questionnaire\Enums\QuestionTypeEnum;
use EscolaLms\Questionnaire\Models\Question;
use EscolaLms\Questionnaire\Models\QuestionAnswer;
use EscolaLms\Questionnaire\Models\Questionnaire;
use EscolaLms\Questionnaire\Models\QuestionnaireModel;
use EscolaLms\Questionnaire\Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class QuestionAnswerTest extends TestCase
{
    use DatabaseTransactions;

    public Questionnaire $questionnaire;
    public QuestionnaireModel $questionnaireModel;
    public Question $question;
    public Question $questionText;

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
        ])->createOne();

        $this->questionText = Question::factory([
                'questionnaire_id' => $this->questionnaire->getKey(),
                'type' => QuestionTypeEnum::TEXT
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

    public function testAdminCanGetAnswerListWithFilters(): void
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
}
