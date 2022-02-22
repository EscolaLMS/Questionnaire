<?php

namespace EscolaLms\Questionnaire\Tests\Api;

use EscolaLms\Questionnaire\Database\Seeders\QuestionnairePermissionsSeeder;
use EscolaLms\Questionnaire\Enums\QuestionTypeEnum;
use EscolaLms\Questionnaire\Models\Question;
use EscolaLms\Questionnaire\Models\QuestionAnswer;
use EscolaLms\Questionnaire\Models\Questionnaire;
use EscolaLms\Questionnaire\Models\QuestionnaireModel;
use EscolaLms\Questionnaire\Tests\TestCase;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class QuestionAnswerTest extends TestCase
{
    use DatabaseTransactions;

    public Questionnaire $questionnaire;
    public QuestionnaireModel $questionnaireModel;
    public Collection $questions;
    public Collection $questionsText;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(QuestionnairePermissionsSeeder::class);
        $this->authenticateAsAdmin();

        $this->questionnaire = Questionnaire::factory()->createOne();
        $this->questionnaireModel = QuestionnaireModel::factory()->createOne();

        $this->questions = Question::factory()
            ->count(5)
            ->create();

        $this->questionsText = Question::factory()
            ->count(2)
            ->create(['type' => QuestionTypeEnum::TEXT]);
    }

    private function uri(int $id, string $modelTypeTitle, int $modelId): string
    {
        return sprintf('/api/questionnaire/%s/%d/%d', $modelTypeTitle, $modelId, $id);
    }

    public function testAdminCanAnswerQuestions(): void
    {
        $arrayOfAnswers = [];
        foreach ($this->questions as $key => $question) {
            $arrayOfAnswers[$question->getKey()] = 5 - $key;
        }
        $arrayOfAnswers[$this->questionsText[0]->getKey()] = null;
        $arrayOfAnswers[$this->questionsText[1]->getKey()] = null;

        $response = $this->actingAs($this->user, 'api')->postJson(
            $this->uri(
                $this->questionnaire->id,
                $this->questionnaireModel->modelableType->title,
                $this->questionnaireModel->model_id
            ),
            [
                'answers' => [
                    ['question_id' => $this->questions[0]->getKey(), 'rate' => 5],
                    ['question_id' => $this->questions[1]->getKey(), 'rate' => 4],
                    ['question_id' => $this->questions[2]->getKey(), 'rate' => 3],
                    ['question_id' => $this->questions[3]->getKey(), 'rate' => 2],
                    ['question_id' => $this->questions[4]->getKey(), 'rate' => 1],
                    ['question_id' => $this->questionsText[0]->getKey(), 'note' => "test_1"],
                    ['question_id' => $this->questionsText[1]->getKey(), 'note' => "test_2"],
                ],
            ]
        );

        $response->assertOk();
        $this->assertEquals(7, QuestionAnswer::count());

        $data = json_decode($response->getContent());

        $this->assertEquals($data->data->id, $this->questionnaire->id);
        foreach ($data->data->questions as $question) {
            $this->assertEquals($question->rate, $arrayOfAnswers[$question->id]);
            if ($question->id === $this->questionsText[0]->getKey()) {
                $this->assertEquals($question->note, 'test_1');
            } elseif ($question->id === $this->questionsText[1]->getKey()) {
                $this->assertEquals($question->note, 'test_2');
            }
        }

        $response = $this->actingAs($this->user, 'api')->postJson(
            $this->uri(
                $this->questionnaire->id,
                $this->questionnaireModel->modelableType->title,
                $this->questionnaireModel->model_id
            ),
            [
                'answers' => [
                    ['question_id' => $this->questions[1]->getKey(), 'rate' => 5],
                    ['question_id' => $this->questions[2]->getKey(), 'rate' => 5],
                    ['question_id' => $this->questions[3]->getKey(), 'rate' => 5],
                    ['question_id' => $this->questions[4]->getKey(), 'rate' => 5],
                    ['question_id' => $this->questionsText[1]->getKey(), 'note' => 'coś innego'],
                ],
            ]
        );

        $response->assertOk();
        $this->assertEquals(7, QuestionAnswer::count());

        $data = json_decode($response->getContent());

        $this->assertEquals($data->data->id, $this->questionnaire->id);
        foreach ($data->data->questions as $question) {
            if ($question->rate) {
                $this->assertEquals($question->rate, 5);
            } else {
                if ($question->id === $this->questionsText[0]->getKey()) {
                    $this->assertEquals($question->note, 'test_1');
                } else {
                    $this->assertEquals($question->note, 'coś innego');
                }
            }
        }
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
                'answers' => [
                    ['question_id' => $this->questions[0]->getKey(), 'rate' => 5],
                    ['question_id' => $this->questions[1]->getKey(), 'rate' => 4],
                    ['question_id' => $this->questions[2]->getKey(), 'rate' => 3],
                    ['question_id' => $this->questions[3]->getKey(), 'rate' => 2],
                    ['question_id' => $this->questions[4]->getKey(), 'rate' => 1],
                ],
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
                'answers' => [
                    ['question_id' => $this->questions[0]->getKey(), 'rate' => 5],
                    ['question_id' => $this->questions[1]->getKey(), 'rate' => 4],
                    ['question_id' => $this->questions[2]->getKey(), 'rate' => 3],
                    ['question_id' => $this->questions[3]->getKey(), 'rate' => 2],
                    ['question_id' => $this->questions[4]->getKey(), 'rate' => 1],
                ],
            ]
        );

        $response->assertForbidden();
        $this->assertEquals(0, QuestionAnswer::count());
    }
}
