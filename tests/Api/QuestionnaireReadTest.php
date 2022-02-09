<?php

namespace EscolaLms\Questionnaire\Tests\Api;

use EscolaLms\Questionnaire\Database\Seeders\QuestionnairePermissionsSeeder;
use EscolaLms\Questionnaire\Models\Question;
use EscolaLms\Questionnaire\Models\QuestionAnswer;
use EscolaLms\Questionnaire\Models\Questionnaire;
use EscolaLms\Questionnaire\Models\QuestionnaireModel;
use EscolaLms\Questionnaire\Models\QuestionnaireModelType;
use EscolaLms\Questionnaire\Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class QuestionnaireReadTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(QuestionnairePermissionsSeeder::class);
    }

    private function uri(int $id, string $modelTypeTitle, int $modelId): string
    {
        return sprintf('/api/questionnaire/%s/%d/%d', $modelTypeTitle, $modelId, $id);
    }

    public function testCanReadExistingQuestionnaire(): void
    {
        $this->authenticateAsAdmin();

        $questionnaire = Questionnaire::factory()->createOne();
        $questionnaireModel = QuestionnaireModel::factory()->createOne();

        Question::factory()
            ->count(20)
            ->create();

        QuestionAnswer::factory()
            ->count(20)
            ->create([
                'user_id' => $this->user->id,
                'questionnaire_model_id' => $questionnaireModel->id
            ]);

        $response = $this->actingAs($this->user, 'api')->getJson(
            $this->uri($questionnaire->id, $questionnaireModel->modelableType->title, $questionnaireModel->model_id)
        );

        $response->assertOk();
    }

    public function testCannotFindMissingQuestionnaire(): void
    {
        $response = $this->getJson($this->uri(99999, 9999, 99999));

        $response->assertStatus(404);
    }

    public function testAdminCanReadExistingQuestionnaireById(): void
    {
        $this->authenticateAsAdmin();

        $questionnaire = Questionnaire::factory()->createOne();

        $response = $this->actingAs($this->user, 'api')->getJson('/api/admin/questionnaire/' . $questionnaire->getKey());
        $response->assertOk();
        $response->assertJsonFragment(collect($questionnaire->getAttributes())->except('id')->toArray());
    }

    public function testCanNotReadNotActiveQuestionnaire(): void
    {
        $this->authenticateAsAdmin();

        $questionnaire = Questionnaire::factory()->createOne(['active' => false]);
        $questionnaireModel = QuestionnaireModel::factory()->createOne();

        $response = $this->actingAs($this->user, 'api')->getJson(
            $this->uri($questionnaire->id, $questionnaireModel->modelableType->title, $questionnaireModel->model_id)
        );

        $response->assertForbidden();
    }

    public function testCanNotReadQuestionnaireWithWrongClassOfModelType(): void
    {
        $this->authenticateAsAdmin();

        $questionnaire = Questionnaire::factory()->createOne();
        $questionnaireModel = QuestionnaireModel::factory()->createOne();
        $questionnaireModelType = QuestionnaireModelType::factory()->createOne([
            'model_class' => Question::class,
            'title' => 'question',
        ]);

        $response = $this->actingAs($this->user, 'api')->getJson(
            $this->uri($questionnaire->id, $questionnaireModelType->title, $questionnaireModel->model_id)
        );

        $response->assertStatus(422);
    }
}
