<?php

namespace EscolaLms\Questionnaire\Tests\Api;

use EscolaLms\Core\Models\User;
use EscolaLms\Core\Tests\CreatesUsers;
use EscolaLms\Courses\Models\Course;
use EscolaLms\Questionnaire\Database\Seeders\QuestionnairePermissionsSeeder;
use EscolaLms\Questionnaire\Models\Question;
use EscolaLms\Questionnaire\Models\QuestionAnswer;
use EscolaLms\Questionnaire\Models\Questionnaire;
use EscolaLms\Questionnaire\Models\QuestionnaireModel;
use EscolaLms\Questionnaire\Models\QuestionnaireModelType;
use EscolaLms\Questionnaire\Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class QuestionAnswerListTest extends TestCase
{
    use DatabaseTransactions, CreatesUsers;

    public Questionnaire $questionnaire;
    public QuestionnaireModel $questionnaireModel;
    public Question $question;
    public Question $questionText;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(QuestionnairePermissionsSeeder::class);
        $this->authenticateAsAdmin();
    }

    public function testAnswersListWithFiltersAndSorts(): void
    {
        $questionnaireModel = QuestionnaireModel::factory()->createOne();
        $questionnaire = Questionnaire::factory()->create();

        $question1 = Question::factory()->create([
            'questionnaire_id' => $questionnaire->getKey(),
            'title' => 'aaaa',
        ]);
        $question2 = Question::factory()->create([
            'questionnaire_id' => $questionnaire->getKey(),
            'title' => 'bbbb',
        ]);

        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $user3 = User::factory()->create();
        $user4 = User::factory()->create();

        $questionAnswer1 = QuestionAnswer::factory()->create([
            'rate' => 1,
            'question_id' => $question1->getKey(),
            'questionnaire_model_id' => $questionnaireModel->getKey(),
            'note' => 'aaaa',
            'user_id' => $user1->getKey(),
        ]);

        $questionAnswer2 = QuestionAnswer::factory()->create([
            'rate' => 2,
            'question_id' => $question1->getKey(),
            'questionnaire_model_id' => $questionnaireModel->getKey(),
            'note' => 'bbbb',
            'user_id' => $user2->getKey(),
        ]);

        $questionAnswer3 = QuestionAnswer::factory()->create([
            'rate' => 3,
            'question_id' => $question2->getKey(),
            'questionnaire_model_id' => $questionnaireModel->getKey(),
            'note' => 'cccc',
            'user_id' => $user3->getKey(),
        ]);

        $questionAnswer4 = QuestionAnswer::factory()->create([
            'rate' => 4,
            'question_id' => $question2->getKey(),
            'questionnaire_model_id' => $questionnaireModel->getKey(),
            'note' => 'dddd',
            'user_id' => $user4->getKey(),
        ]);

        $response = $this->actingAs($this->user, 'api')->json(
            'get',
            '/api/admin/question-answers/' . $questionnaire->getKey(),
            [
                'questionnaire_model_id' => $questionnaireModel->getKey(),
                'order_by' => 'id',
                'order' => 'ASC',
            ]
        );

        $this->assertTrue($response->json('data.0.id') === $questionAnswer1->getKey());
        $this->assertTrue($response->json('data.1.id') === $questionAnswer2->getKey());

        $response = $this->actingAs($this->user, 'api')->json(
            'get',
            '/api/admin/question-answers/' . $questionnaire->getKey(),
            [
                'questionnaire_model_id' => $questionnaireModel->getKey(),
                'order_by' => 'id',
                'order' => 'DESC',
            ]
        );


        $this->assertTrue($response->json('data.0.id') === $questionAnswer4->getKey());
        $this->assertTrue($response->json('data.1.id') === $questionAnswer3->getKey());

        $response = $this->actingAs($this->user, 'api')->json(
            'get',
            '/api/admin/question-answers/' . $questionnaire->getKey(),
            [
                'questionnaire_model_id' => $questionnaireModel->getKey(),
                'order_by' => 'user_id',
                'order' => 'ASC',
            ]
        );

        $this->assertTrue($response->json('data.0.id') === $questionAnswer1->getKey());
        $this->assertTrue($response->json('data.1.id') === $questionAnswer2->getKey());

        $response = $this->actingAs($this->user, 'api')->json(
            'get',
            '/api/admin/question-answers/' . $questionnaire->getKey(),
            [
                'questionnaire_model_id' => $questionnaireModel->getKey(),
                'order_by' => 'user_id',
                'order' => 'DESC',
            ]
        );

        $this->assertTrue($response->json('data.0.id') === $questionAnswer4->getKey());
        $this->assertTrue($response->json('data.1.id') === $questionAnswer3->getKey());

        $response = $this->actingAs($this->user, 'api')->json(
            'get',
            '/api/admin/question-answers/' . $questionnaire->getKey(),
            [
                'questionnaire_model_id' => $questionnaireModel->getKey(),
                'order_by' => 'note',
                'order' => 'ASC',
            ]
        );

        $this->assertTrue($response->json('data.0.id') === $questionAnswer1->getKey());
        $this->assertTrue($response->json('data.1.id') === $questionAnswer2->getKey());

        $response = $this->actingAs($this->user, 'api')->json(
            'get',
            '/api/admin/question-answers/' . $questionnaire->getKey(),
            [
                'questionnaire_model_id' => $questionnaireModel->getKey(),
                'order_by' => 'note',
                'order' => 'DESC',
            ]
        );

        $this->assertTrue($response->json('data.0.id') === $questionAnswer4->getKey());
        $this->assertTrue($response->json('data.1.id') === $questionAnswer3->getKey());

        $response = $this->actingAs($this->user, 'api')->json(
            'get',
            '/api/admin/question-answers/' . $questionnaire->getKey(),
            [
                'questionnaire_model_id' => $questionnaireModel->getKey(),
                'order_by' => 'rate',
                'order' => 'ASC',
            ]
        );

        $this->assertTrue($response->json('data.0.id') === $questionAnswer1->getKey());
        $this->assertTrue($response->json('data.1.id') === $questionAnswer2->getKey());

        $response = $this->actingAs($this->user, 'api')->json(
            'get',
            '/api/admin/question-answers/' . $questionnaire->getKey(),
            [
                'questionnaire_model_id' => $questionnaireModel->getKey(),
                'order_by' => 'rate',
                'order' => 'DESC',
            ]
        );

        $this->assertTrue($response->json('data.0.id') === $questionAnswer4->getKey());
        $this->assertTrue($response->json('data.1.id') === $questionAnswer3->getKey());

        $response = $this->actingAs($this->user, 'api')->json(
            'get',
            '/api/admin/question-answers/' . $questionnaire->getKey(),
            [
                'questionnaire_model_id' => $questionnaireModel->getKey(),
                'order_by' => 'question_title',
                'order' => 'ASC',
            ]
        );

        $this->assertTrue($response->json('data.0.id') === $questionAnswer1->getKey());
        $this->assertTrue($response->json('data.1.id') === $questionAnswer2->getKey());

        $response = $this->actingAs($this->user, 'api')->json(
            'get',
            '/api/admin/question-answers/' . $questionnaire->getKey(),
            [
                'questionnaire_model_id' => $questionnaireModel->getKey(),
                'order_by' => 'question_title',
                'order' => 'DESC',
            ]
        );

        $this->assertTrue($response->json('data.0.id') === $questionAnswer3->getKey());
        $this->assertTrue($response->json('data.1.id') === $questionAnswer4->getKey());

        $response = $this->actingAs($this->user, 'api')->json(
            'get',
            '/api/admin/question-answers/' . $questionnaire->getKey(),
            [
                'questionnaire_model_id' => $questionnaireModel->getKey(),
                'user_id' => $user1->getKey(),
            ]
        );
        $response->assertJsonCount(1, 'data');
        $this->assertTrue($response->json('data.0.id') === $questionAnswer1->getKey());
    }

    public function testListAnswersOnlyAuthoredModels(): void
    {
        $tutor = $this->makeInstructor();
        $tutor2 = $this->makeInstructor();

        $questionnaire = Questionnaire::factory([
            'title' => 'Only authored',
        ])->create();

        $course1 = Course::factory()->create();
        $course1->authors()->sync($tutor);

        $course2 = Course::factory()->create();
        $course2->authors()->sync($tutor2);

        $questionnaireModelType = QuestionnaireModelType::query()->where('model_class', '=', Course::class)->first();
        if (empty($questionnaireModelType)) {
            $questionnaireModelType = QuestionnaireModelType::factory()->createOne();
        }

        $questionnaireModel1 = $questionnaire->questionnaireModels()->save(QuestionnaireModel::factory()->make([
            'model_id' => $course1->getKey(),
            'model_type_id' => $questionnaireModelType->getKey(),
        ]));

        $questionnaireModel2 = $questionnaire->questionnaireModels()->save(QuestionnaireModel::factory()->make([
            'model_id' => $course2->getKey(),
            'model_type_id' => $questionnaireModelType->getKey(),
        ]));

        $question = Question::factory()->create([
            'questionnaire_id' => $questionnaire->getKey(),
            'title' => 'Question',
        ]);

        $student = $this->makeStudent();

        $questionAnswer1 = QuestionAnswer::factory()->create([
            'rate' => 1,
            'question_id' => $question->getKey(),
            'questionnaire_model_id' => $questionnaireModel1->getKey(),
            'note' => 'aaaa',
            'user_id' => $student->getKey(),
        ]);

        $questionAnswer2 = QuestionAnswer::factory()->create([
            'rate' => 2,
            'question_id' => $question->getKey(),
            'questionnaire_model_id' => $questionnaireModel2->getKey(),
            'note' => 'bbbb',
            'user_id' => $student->getKey(),
        ]);

        $this
            ->actingAs($tutor, 'api')
            ->json('GET', '/api/admin/question-answers/' . $questionnaire->getKey())
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment([
                'id' => $questionAnswer1->getKey(),
                'rate' => 1,
                'note' => 'aaaa',
            ])
            ->assertJsonMissing([
                'id' => $questionAnswer2->getKey(),
                'rate' => 2,
                'note' => 'bbbb',
            ]);
    }
}
