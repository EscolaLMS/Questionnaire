<?php

namespace EscolaLms\Questionnaire\Tests\Api;

use EscolaLms\Questionnaire\Models\Questionnaire;
use EscolaLms\Questionnaire\Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class QuestionnaireUpdateTest extends TestCase
{
    use DatabaseTransactions;

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
                'model' => $questionnaireNew->model,
                'model_id' => $questionnaireNew->model_id,
            ]
        );
        $response->assertOk();
        $questionnaire->refresh();

        $this->assertEquals($questionnaireNew->title, $questionnaire->title);
        $this->assertEquals($questionnaireNew->model, $questionnaire->model);
        $this->assertEquals($questionnaireNew->model_id, $questionnaire->model_id);
    }

    public function testAdminCanUpdateExistingQuestionnaireWithMissingTitle(): void
    {
        $this->authenticateAsAdmin();

        $questionnaire = Questionnaire::factory()->createOne();
        $questionnaireNew = Questionnaire::factory()->makeOne();
        $oldTitle = $questionnaire->title;

        $response = $this->actingAs($this->user, 'api')->patchJson(
            $this->uri($questionnaire->id),
            [
                'model' => $questionnaireNew->model,
            ]
        );
        $response->assertStatus(200);
        $questionnaire->refresh();

        $this->assertEquals($oldTitle, $questionnaire->title);
        $this->assertEquals($questionnaireNew->model, $questionnaire->model);
    }

    public function testAdminCanUpdateExistingQuestionnaireWithMissingModel(): void
    {
        $this->authenticateAsAdmin();

        $questionnaire = Questionnaire::factory()->createOne();
        $questionnaireNew = Questionnaire::factory()->makeOne();
        $oldModel = $questionnaire->model;

        $response = $this->actingAs($this->user, 'api')->patchJson(
            $this->uri($questionnaire->id),
            [
                'title' => $questionnaireNew->title,
            ]
        );
        $response->assertStatus(200);
        $questionnaire->refresh();

        $this->assertEquals($questionnaireNew->title, $questionnaire->title);
        $this->assertEquals($oldModel, $questionnaire->model);
    }

    public function testAdminCannotUpdateMissingQuestionnaire(): void
    {
        $this->authenticateAsAdmin();

        $questionnaire = Questionnaire::factory()->makeOne();

        $response = $this->actingAs($this->user, 'api')->patchJson(
            $this->uri(99999999),
            [
                'title' => $questionnaire->title,
                'model' => $questionnaire->model,
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
        $oldModel = $questionnaire->model;

        $response = $this->patchJson(
            $this->uri($questionnaire->id),
            [
                'title' => $questionnaireNew->title,
                'model' => $questionnaireNew->model,
            ]
        );
        $response->assertUnauthorized();
        $questionnaire->refresh();

        $this->assertEquals($oldTitle, $questionnaire->title);
        $this->assertEquals($oldModel, $questionnaire->model);
    }
}
