<?php

namespace EscolaLms\Questionnaire\Tests\Api;

use EscolaLms\Questionnaire\Database\Seeders\QuestionnairePermissionsSeeder;
use EscolaLms\Questionnaire\Models\Questionnaire;
use EscolaLms\Questionnaire\Models\QuestionnaireModel;
use EscolaLms\Questionnaire\Models\QuestionnaireModelType;
use EscolaLms\Questionnaire\Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class QuestionnaireCreateTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(QuestionnairePermissionsSeeder::class);
    }

    public function testAdminCanCreateQuestionnaire(): void
    {
        $this->authenticateAsAdmin();
        $questionnaire = Questionnaire::factory()->makeOne(['active' => false]);

        $response = $this->actingAs($this->user, 'api')->postJson(
            '/api/admin/questionnaire',
            $questionnaire->toArray()
        );

        $response->assertStatus(201);

        $response2 = $this->actingAs($this->user, 'api')->getJson(
            '/api/admin/questionnaire/' . $questionnaire->id,
        );

        $response2->assertOk();
    }

    public function testAdminCannotCreateQuestionnaireWithoutTitle(): void
    {
        $this->authenticateAsAdmin();

        $questionnaire = Questionnaire::factory()->makeOne();
        $response = $this->actingAs($this->user, 'api')->postJson(
            '/api/admin/questionnaire',
            collect($questionnaire->getAttributes())->except('id', 'title')->toArray()
        );
        $response->assertStatus(422);
    }

    public function testGuestCannotCreateQuestionnaire(): void
    {
        $questionnaire = Questionnaire::factory()->makeOne();
        $response = $this->postJson(
            '/api/admin/questionnaire',
            collect($questionnaire->getAttributes())->except('id')->toArray()
        );
        $response->assertUnauthorized();
    }

    public function testAdminCanCreateQuestionnaireWithModels(): void
    {
        $this->authenticateAsAdmin();
        $questionnaireModelType = QuestionnaireModelType::query()->inRandomOrder()->first();
        if (empty($questionnaireModelType)) {
            $questionnaireModelType = QuestionnaireModelType::factory()->createOne();
        }
        $questionnaire = Questionnaire::factory()->makeOne();
        $questionnaireModel = QuestionnaireModel::factory()->makeOne();
        $model = new $questionnaireModelType->model_class();
        $newModel = $model::factory()
            ->count(1)
            ->create();
        $questionnaireModel2 = QuestionnaireModel::factory()->makeOne(['model_id' => $newModel[0]->id]);

        $response = $this->actingAs($this->user, 'api')->postJson(
            '/api/admin/questionnaire',
            [
                'title' => $questionnaire->title,
                'active' => $questionnaire->active,
                'models' => [
                    [
                        'model_type_id' => $questionnaireModel->model_type_id,
                        'model_id' => $questionnaireModel->model_id,
                    ],
                    [
                        'model_type_id' => $questionnaireModel2->model_type_id,
                        'model_id' => $questionnaireModel2->model_id,
                    ],
                ],
            ]
        );

        $response->assertStatus(201);

        $data = json_decode($response->getContent());
        $questionnaire = Questionnaire::find($data->data->id);

        $response2 = $this->getJson(
            sprintf(
                '/api/questionnaire/%s/%d/%d',
                $questionnaireModel->modelableType->title,
                $questionnaireModel->model_id,
                $questionnaire->id
            )
        );

        $response2->assertOk();

        $response3 = $this->getJson(
            sprintf(
                '/api/questionnaire/%s/%d/%d',
                $questionnaireModel2->modelableType->title,
                $questionnaireModel2->model_id,
                $questionnaire->id
            )
        );

        $response3->assertOk();

        $response4 = $this->actingAs($this->user, 'api')->getJson(
            '/api/admin/questionnaire/' . $questionnaire->id,
        );

        $response4->assertOk();
    }
}
