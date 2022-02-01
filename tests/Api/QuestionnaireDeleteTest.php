<?php

namespace EscolaLms\Questionnaire\Tests\Api;

use EscolaLms\Questionnaire\Models\Questionnaire;
use EscolaLms\Questionnaire\Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class QuestionnaireDeleteTest extends TestCase
{
    use DatabaseTransactions;

    private function uri(int $id): string
    {
        return sprintf('/api/admin/questionnaire/%d', $id);
    }

    public function testAdminCanDeleteExistingQuestionnaire(): void
    {
        $this->authenticateAsAdmin();

        $questionnaire = Questionnaire::factory()->createOne();
        $response = $this->actingAs($this->user, 'api')->delete($this->uri($questionnaire->id));
        $response->assertOk();
        $this->assertEquals(0, Questionnaire::where('id', $questionnaire->id)->count());
    }

    public function testAdminCannotDeleteMissingQuestionnaire(): void
    {
        $this->authenticateAsAdmin();

        $questionnaire = Questionnaire::factory()->makeOne();
        $questionnaire->id = 999999;

        $response = $this->actingAs($this->user, 'api')->delete($this->uri($questionnaire->id));

        $response->assertStatus(404);
    }

    public function testGuestCannotDeleteExistingQuestionnaire(): void
    {
        $questionnaire = Questionnaire::factory()->createOne();
        $response = $this->json('delete', $this->uri($questionnaire->id));
        $response->assertUnauthorized();
    }
}
