<?php

namespace EscolaLms\Questionnaire\Tests\Api;

use EscolaLms\Questionnaire\Database\Seeders\QuestionnairePermissionsSeeder;
use EscolaLms\Questionnaire\Models\Questionnaire;
use EscolaLms\Questionnaire\Models\QuestionnaireModel;
use EscolaLms\Questionnaire\Models\QuestionnaireModelType;
use EscolaLms\Questionnaire\Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class QuestionnaireUpdateTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(QuestionnairePermissionsSeeder::class);
    }

    private function uri(int $id): string
    {
        return sprintf('/api/admin/questionnaire/%d', $id);
    }

    public function testAdminCanUpdateExistingQuestionnaire(): void
    {
        $this->authenticateAsAdmin();

        $questionnaire = Questionnaire::factory()->createOne();
        $questionnaireNew = Questionnaire::factory()->makeOne();

        $response = $this->actingAs($this->user, 'api')->patchJson(
            $this->uri($questionnaire->id),
            [
                'title' => $questionnaireNew->title,
            ]
        );
        $response->assertOk();
        $questionnaire->refresh();

        $this->assertEquals($questionnaireNew->title, $questionnaire->title);
    }

    public function testAdminCanUpdateExistingQuestionnaireWithMissingTitle(): void
    {
        $this->authenticateAsAdmin();

        $questionnaire = Questionnaire::factory()->createOne();
        $questionnaireNew = Questionnaire::factory()->makeOne();
        $oldTitle = $questionnaire->title;

        $response = $this->actingAs($this->user, 'api')->patchJson(
            $this->uri($questionnaire->id),
            []
        );
        $response->assertStatus(200);
        $questionnaire->refresh();

        $this->assertEquals($oldTitle, $questionnaire->title);
    }

    public function testAdminCannotUpdateMissingQuestionnaire(): void
    {
        $this->authenticateAsAdmin();

        $questionnaire = Questionnaire::factory()->makeOne();

        $response = $this->actingAs($this->user, 'api')->patchJson(
            $this->uri(99999999),
            [
                'title' => $questionnaire->title,
            ]
        );

        $response->assertStatus(404);
        $this->assertEquals(0, $questionnaire->newQuery()->where('id', $questionnaire->id)->count());
    }

    public function testGuestCannotUpdateExistingQuestionnaire(): void
    {
        $questionnaire = Questionnaire::factory()->createOne();
        $questionnaireNew = Questionnaire::factory()->makeOne();

        $oldTitle = $questionnaire->title;

        $response = $this->patchJson(
            $this->uri($questionnaire->id),
            [
                'title' => $questionnaireNew->title,
            ]
        );
        $response->assertUnauthorized();
        $questionnaire->refresh();

        $this->assertEquals($oldTitle, $questionnaire->title);
    }

    public function testAdminCanUpdateQuestionnaireWithModels(): void
    {
        $this->authenticateAsAdmin();
        $questionnaireModelType = QuestionnaireModelType::factory()->createOne();
        $questionnaire = Questionnaire::factory()->createOne();
        $questionnaireNew = Questionnaire::factory()->makeOne();
        $questionnaireModel = QuestionnaireModel::factory()->createOne();
        $model = new $questionnaireModelType->model_class();
        $newModel = $model::factory()
            ->count(2)
            ->create();
        $questionnaireModel2 = QuestionnaireModel::factory()->createOne(['model_id' => $newModel[0]->id]);
        $questionnaireModel3 = QuestionnaireModel::factory()->makeOne(['model_id' => $newModel[1]->id]);

        $responseModel2Exist = $this->actingAs($this->user, 'api')->getJson(
            sprintf(
                '/api/questionnaire/%s/%d/%d',
                $questionnaireModel2->modelableType->title,
                $questionnaireModel2->model_id,
                $questionnaire->id
            )
        );

        $responseModel2Exist->assertOk();
        $this->assertEquals(2, QuestionnaireModel::count());

        $response = $this->actingAs($this->user, 'api')->patchJson(
            $this->uri($questionnaire->id),
            [
                'title' => $questionnaireNew->title,
                'models' => [
                    [
                        'model_type_id' => $questionnaireModel->model_type_id,
                        'model_id' => $questionnaireModel->model_id,
                    ],
                    [
                        'model_type_id' => $questionnaireModel3->model_type_id,
                        'model_id' => $questionnaireModel3->model_id,
                    ],
                ],
            ]
        );
        $response->assertOk();

        $questionnaire->refresh();

        $this->assertEquals($questionnaireNew->title, $questionnaire->title);
        $this->assertEquals(2, QuestionnaireModel::count());

        $responseModel1Exist = $this->getJson(
            sprintf(
                '/api/questionnaire/%s/%d/%d',
                $questionnaireModel->modelableType->title,
                $questionnaireModel->model_id,
                $questionnaire->id
            )
        );

        $responseModel1Exist->assertOk();

        $responseModel2NotExist = $this->getJson(
            sprintf(
                '/api/questionnaire/%s/%d/%d',
                $questionnaireModel2->modelableType->title,
                $questionnaireModel2->model_id,
                $questionnaire->id
            )
        );

        $responseModel2NotExist->assertNotFound();

        $responseModel3Exist = $this->getJson(
            sprintf(
                '/api/questionnaire/%s/%d/%d',
                $questionnaireModel3->modelableType->title,
                $questionnaireModel3->model_id,
                $questionnaire->id
            )
        );

        $responseModel3Exist->assertOk();

        $questionnaire->refresh();

        $this->assertEquals($questionnaireNew->title, $questionnaire->title);
    }
}
