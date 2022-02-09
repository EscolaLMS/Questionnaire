<?php

namespace EscolaLms\Questionnaire\Tests\Api;

use EscolaLms\Questionnaire\Database\Seeders\QuestionnairePermissionsSeeder;
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

    public function testAnonymousCantListEmptyQuestionnaire(): void
    {
        $questionnaireModel = QuestionnaireModel::factory()->createOne();
        $url = sprintf('/api/questionnaire/%s/%d', $questionnaireModel->modelableType->title, $questionnaireModel->model_id);

        $response = $this->getJson($url);

        $response->assertForbidden();
    }

    public function testAnonymousCantListQuestionnaire(): void
    {
        $questionnaireModel = QuestionnaireModel::factory()->createOne();
        $url = sprintf('/api/questionnaire/%s/%d', $questionnaireModel->modelableType->title, $questionnaireModel->model_id);
        Questionnaire::factory()
            ->count(10)
            ->create(['active' => true]);

        $response = $this->getJson($url);

        $response->assertForbidden();
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
}
