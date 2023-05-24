<?php

namespace EscolaLms\Questionnaire\Tests\Api;

use EscolaLms\Questionnaire\Database\Seeders\QuestionnairePermissionsSeeder;
use EscolaLms\Questionnaire\Enums\QuestionTypeEnum;
use EscolaLms\Questionnaire\Models\Question;
use EscolaLms\Questionnaire\Models\Questionnaire;
use EscolaLms\Questionnaire\Models\QuestionnaireModel;
use EscolaLms\Questionnaire\Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class QuestionnaireListTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(QuestionnairePermissionsSeeder::class);
    }

    public function testAdminCanListEmptyQuestionnaire(): void
    {
        $this->authenticateAsAdmin();

        $response = $this->actingAs($this->user, 'api')->getJson('/api/admin/questionnaire');

        $response->assertOk();

        $response->assertJsonStructure([
            'success',
            'data',
            'meta',
            'message'
        ]);

        $response->assertJsonCount(0, 'data');
    }

    public function testAdminCanListQuestionnaire(): void
    {
        $this->authenticateAsAdmin();

        $questionnaires = Questionnaire::factory()
            ->count(10)
            ->create();

        $questionnairesArr = $questionnaires->map(function (Questionnaire $p) {
            return $p->toArray();
        })->toArray();

        $response = $this->actingAs($this->user, 'api')->getJson('/api/admin/questionnaire');
        $response->assertOk();
        $response->assertJsonFragment(
            $questionnairesArr[0],
        );
    }

    public function testListQuestionnaire(): void
    {
        $this->authenticateAsAdmin();
        $questionnaireModel = QuestionnaireModel::factory()->createOne();
        $url = sprintf('/api/questionnaire/%s/%d', $questionnaireModel->modelableType->title, $questionnaireModel->model_id);
        Questionnaire::factory()
            ->count(10)
            ->create(['active' => true]);

        $response = $this->actingAs($this->user, 'api')->getJson($url);

        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'data' => [[
                'id',
                'title',
                'active',
            ]],
            'meta',
            'message'
        ]);
    }

    public function testAdminCanListQuestionnaireWithFiltersAndSorts(): void
    {
        $this->authenticateAsAdmin();

        $questionnaireOne = Questionnaire::factory()->create([
            'title' => 'one',
            'active' => true,
            'created_at' => now()->subDays(1),
        ]);

        $questionnaireTwo = Questionnaire::factory()->create([
            'title' => 'two',
            'active' => true,
            'created_at' => now(),
        ]);

        $this
            ->actingAs($this->user, 'api')
            ->json('GET', '/api/admin/questionnaire', [
                'title' => 'on',
            ])
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment([
                'id' => $questionnaireOne->id,
            ]);

        $response = $this
            ->actingAs($this->user, 'api')
            ->json('GET', '/api/admin/questionnaire', [
                'order_by' => 'created_at',
                'order' => 'ASC',
            ]);

        $this->assertTrue($response->json('data.0.id') === $questionnaireOne->getKey());
        $this->assertTrue($response->json('data.1.id') === $questionnaireTwo->getKey());

        $response = $this
            ->actingAs($this->user, 'api')
            ->json('GET', '/api/admin/questionnaire', [
                'order_by' => 'created_at',
                'order' => 'DESC',
            ]);

        $this->assertTrue($response->json('data.0.id') === $questionnaireTwo->getKey());
        $this->assertTrue($response->json('data.1.id') === $questionnaireOne->getKey());

        $response = $this
            ->actingAs($this->user, 'api')
            ->json('GET', '/api/admin/questionnaire', [
                'order_by' => 'title',
                'order' => 'ASC',
            ]);

        $this->assertTrue($response->json('data.0.id') === $questionnaireOne->getKey());
        $this->assertTrue($response->json('data.1.id') === $questionnaireTwo->getKey());

        $response = $this
            ->actingAs($this->user, 'api')
            ->json('GET', '/api/admin/questionnaire', [
                'order_by' => 'title',
                'order' => 'DESC',
            ]);

        $this->assertTrue($response->json('data.0.id') === $questionnaireTwo->getKey());
        $this->assertTrue($response->json('data.1.id') === $questionnaireOne->getKey());

        $response = $this
            ->actingAs($this->user, 'api')
            ->json('GET', '/api/admin/questionnaire', [
                'order_by' => 'id',
                'order' => 'ASC',
            ]);

        $this->assertTrue($response->json('data.0.id') === $questionnaireOne->getKey());
        $this->assertTrue($response->json('data.1.id') === $questionnaireTwo->getKey());

        $response = $this
            ->actingAs($this->user, 'api')
            ->json('GET', '/api/admin/questionnaire', [
                'order_by' => 'id',
                'order' => 'DESC',
            ]);

        $this->assertTrue($response->json('data.0.id') === $questionnaireTwo->getKey());
        $this->assertTrue($response->json('data.1.id') === $questionnaireOne->getKey());
    }

    public function testAnonymousCanListEmptyQuestionnaire(): void
    {
        $questionnaireModel = QuestionnaireModel::factory()->createOne();
        $url = sprintf('/api/questionnaire/%s/%d', $questionnaireModel->modelableType->title, $questionnaireModel->model_id);

        $response = $this->getJson($url);

        $response->assertOk();
    }

    public function testAnonymousCanListQuestionnaire(): void
    {
        $questionnaireModel = QuestionnaireModel::factory()->createOne();
        $url = sprintf('/api/questionnaire/%s/%d', $questionnaireModel->modelableType->title, $questionnaireModel->model_id);
        Questionnaire::factory()
            ->count(10)
            ->create(['active' => true]);

        $response = $this->getJson($url);

        $response->assertOk();
    }

    public function testListWithActiveAndNotActiveQuestionnaire(): void
    {
        $this->authenticateAsAdmin();
        $questionnaireModel = QuestionnaireModel::factory()->createOne();
        $url = sprintf('/api/questionnaire/%s/%d', $questionnaireModel->modelableType->title, $questionnaireModel->model_id);
        $questionnaires = Questionnaire::factory()
            ->count(5)
            ->create(['active' => true]);
        foreach ($questionnaires as $questionnaire) {
            QuestionnaireModel::factory()->createOne(
                [
                    'questionnaire_id' => $questionnaire->getKey(),
                    'model_type_id' => $questionnaireModel->model_type_id,
                    'model_id' => $questionnaireModel->model_id,
                ]
            );
        }
        $questionnaires = Questionnaire::factory()
            ->count(5)
            ->create(['active' => false]);
        foreach ($questionnaires as $questionnaire) {
            QuestionnaireModel::factory()->createOne(
                [
                    'questionnaire_id' => $questionnaire->getKey(),
                    'model_type_id' => $questionnaireModel->model_type_id,
                    'model_id' => $questionnaireModel->model_id,
                ]
            );
        }

        $response = $this->actingAs($this->user, 'api')->getJson($url);
        $data = json_decode($response->getContent());

        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'data' => [[
                'id',
                'title',
                'active',
            ]],
            'meta',
            'message'
        ]);
        $this->assertEquals(6, count($data->data));
    }

    public function testListQuestionsWithPublicAnswers(): void
    {
        $this->authenticateAsAdmin();
        $questionnaireOnlyPublic = Questionnaire::factory(['title' => 'Only public', 'active' => true])->create();
        $questionnaireOnlyHidden = Questionnaire::factory(['title' => 'Only hidden', 'active' => true])->create();
        $questionnaire = Questionnaire::factory(['title' => 'Public and hidden', 'active' => true])->create();

        $model = QuestionnaireModel::factory()->createOne([
            'questionnaire_id' => $questionnaireOnlyPublic->getKey(),
        ]);
        QuestionnaireModel::factory()->createOne([
            'questionnaire_id' => $questionnaireOnlyHidden->getKey(),
            'model_type_id' => $model->model_type_id,
            'model_id' => $model->model_id,
        ]);
        QuestionnaireModel::factory()->createOne([
            'questionnaire_id' => $questionnaire->getKey(),
            'model_type_id' => $model->model_type_id,
            'model_id' => $model->model_id,
        ]);

        $publicQuestion = Question::factory()->create([
            'questionnaire_id' => $questionnaireOnlyPublic->getKey(),
            'public_answers' => true,
            'active' => true,
            'title' => 'Only public question'
        ]);
        $hiddenQuestion = Question::factory()->create([
            'questionnaire_id' => $questionnaireOnlyHidden->getKey(),
            'public_answers' => false,
            'active' => true,
            'title' => 'Only hidden question'
        ]);
        $publicQuestion2 = Question::factory()->create([
            'questionnaire_id' => $questionnaire->getKey(),
            'public_answers' => true,
            'active' => true,
            'title' => 'Hidden question'
        ]);
        $hiddenQuestion2 = Question::factory()->create([
            'questionnaire_id' => $questionnaire->getKey(),
            'public_answers' => false,
            'active' => true,
            'title' => 'Public question'
        ]);

        $this
            ->actingAs($this->user)
            ->json('GET', '/api/questionnaire/' . $model->modelableType->title . '/' . $model->model_id, ['public_answers' => true])
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonFragment([
                'id' => $questionnaireOnlyPublic->getKey(),
                'title' => $questionnaireOnlyPublic->title,
            ])
            ->assertJsonFragment([
                'title' => $publicQuestion->title,
                'public_answers' => true,
            ])
            ->assertJsonFragment([
                'id' => $questionnaire->getKey(),
                'title' => $questionnaire->title,
            ])
            ->assertJsonFragment([
                'title' => $publicQuestion2->title,
                'public_answers' => true,
            ])
            ->assertJsonMissing([
                'id' => $questionnaireOnlyHidden->getKey(),
                'title' => $questionnaireOnlyHidden->title,
            ])
            ->assertJsonMissing([
                'title' => $hiddenQuestion->title,
                'public_answers' => false,
            ])
            ->assertJsonMissing([
                'title' => $hiddenQuestion2->title,
                'public_answers' => false,
            ]);

        $this
            ->actingAs($this->user)
            ->json('GET', '/api/questionnaire/' . $model->modelableType->title . '/' . $model->model_id, ['public_answers' => false])
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonMissing([
                'id' => $questionnaireOnlyPublic->getKey(),
                'title' => $questionnaireOnlyPublic->title,
            ])
            ->assertJsonMissing([
                'title' => $publicQuestion->title,
                'public_answers' => true,
            ])
            ->assertJsonMissing([
                'title' => $publicQuestion2->title,
                'public_answers' => true,
            ])
            ->assertJsonFragment([
                'id' => $questionnaireOnlyHidden->getKey(),
                'title' => $questionnaireOnlyHidden->title,
            ])
            ->assertJsonFragment([
                'id' => $questionnaire->getKey(),
                'title' => $questionnaire->title,
            ])
            ->assertJsonFragment([
                'title' => $hiddenQuestion->title,
                'public_answers' => false,
            ])
            ->assertJsonFragment([
                'title' => $hiddenQuestion2->title,
                'public_answers' => false,
            ]);
    }

    public function testListQuestionsType(): void
    {
        $this->authenticateAsAdmin();
        $questionnaireOnlyRate = Questionnaire::factory(['title' => 'Only rate', 'active' => true])->create();
        $questionnaireOnlyReview = Questionnaire::factory(['title' => 'Only review', 'active' => true])->create();
        $questionnaireOnlyText = Questionnaire::factory(['title' => 'Only text', 'active' => true])->create();

        $model = QuestionnaireModel::factory()->createOne([
            'questionnaire_id' => $questionnaireOnlyRate->getKey(),
        ]);
        QuestionnaireModel::factory()->createOne([
            'questionnaire_id' => $questionnaireOnlyReview->getKey(),
            'model_type_id' => $model->model_type_id,
            'model_id' => $model->model_id,
        ]);
        QuestionnaireModel::factory()->createOne([
            'questionnaire_id' => $questionnaireOnlyText->getKey(),
            'model_type_id' => $model->model_type_id,
            'model_id' => $model->model_id,
        ]);

        $rateQuestion = Question::factory()->create([
            'questionnaire_id' => $questionnaireOnlyRate->getKey(),
            'public_answers' => true,
            'active' => true,
            'title' => 'Only rate question',
            'type' => QuestionTypeEnum::RATE,
        ]);
        $reviewQuestion = Question::factory()->create([
            'questionnaire_id' => $questionnaireOnlyReview->getKey(),
            'public_answers' => true,
            'active' => true,
            'title' => 'Only review question',
            'type' => QuestionTypeEnum::REVIEW,
        ]);
        $textQuestion = Question::factory()->create([
            'questionnaire_id' => $questionnaireOnlyText->getKey(),
            'public_answers' => true,
            'active' => true,
            'title' => 'Only text question',
            'type' => QuestionTypeEnum::TEXT,
        ]);

        $this
            ->actingAs($this->user)
            ->json('GET', '/api/questionnaire/' . $model->modelableType->title . '/' . $model->model_id, ['question_type' => QuestionTypeEnum::RATE])
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment([
                'id' => $questionnaireOnlyRate->getKey(),
                'title' => $questionnaireOnlyRate->title,
            ])
            ->assertJsonFragment([
                'title' => $rateQuestion->title,
            ])
            ->assertJsonMissing([
                'id' => $questionnaireOnlyReview->getKey(),
                'title' => $questionnaireOnlyReview->title,
            ])
            ->assertJsonMissing([
                'title' => $reviewQuestion->title,
            ])
            ->assertJsonMissing([
                'id' => $questionnaireOnlyText->getKey(),
                'title' => $questionnaireOnlyText->title,
            ])
            ->assertJsonMissing([
                'title' => $textQuestion->title,
            ]);

        $this
            ->actingAs($this->user)
            ->json('GET', '/api/questionnaire/' . $model->modelableType->title . '/' . $model->model_id, ['question_type' => QuestionTypeEnum::REVIEW])
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonMissing([
                'id' => $questionnaireOnlyRate->getKey(),
                'title' => $questionnaireOnlyRate->title,
            ])
            ->assertJsonMissing([
                'title' => $rateQuestion->title,
            ])
            ->assertJsonFragment([
                'id' => $questionnaireOnlyReview->getKey(),
                'title' => $questionnaireOnlyReview->title,
            ])
            ->assertJsonFragment([
                'title' => $reviewQuestion->title,
            ])
            ->assertJsonMissing([
                'id' => $questionnaireOnlyText->getKey(),
                'title' => $questionnaireOnlyText->title,
            ])
            ->assertJsonMissing([
                'title' => $textQuestion->title,
            ]);

        $this
            ->actingAs($this->user)
            ->json('GET', '/api/questionnaire/' . $model->modelableType->title . '/' . $model->model_id, ['question_type' => QuestionTypeEnum::TEXT])
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonMissing([
                'id' => $questionnaireOnlyRate->getKey(),
                'title' => $questionnaireOnlyRate->title,
            ])
            ->assertJsonMissing([
                'title' => $rateQuestion->title,
            ])
            ->assertJsonMissing([
                'id' => $questionnaireOnlyReview->getKey(),
                'title' => $questionnaireOnlyReview->title,
            ])
            ->assertJsonMissing([
                'title' => $reviewQuestion->title,
            ])
            ->assertJsonFragment([
                'id' => $questionnaireOnlyText->getKey(),
                'title' => $questionnaireOnlyText->title,
            ])
            ->assertJsonFragment([
                'title' => $textQuestion->title,
            ]);
    }
}
