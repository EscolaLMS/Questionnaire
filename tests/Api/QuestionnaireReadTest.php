<?php

namespace EscolaLms\Questionnaire\Tests\Api;

use EscolaLms\Questionnaire\Models\Questionnaire;
use EscolaLms\Questionnaire\Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class QuestionnaireReadTest extends TestCase
{
    use DatabaseTransactions;

    private function uri(int $id): string
    {
        return sprintf('/api/questionnaire/%d', $id);
    }

    public function testCanReadExistingQuestionnaire(): void
    {
        $questionnaire = Questionnaire::factory()->createOne();

        $response = $this->getJson($this->uri($questionnaire->id));

        $response->assertOk();
        $response->assertJsonFragment(collect($questionnaire->getAttributes())->except('id')->toArray());
    }

    public function testCannotFindMissingQuestionnaire(): void
    {
        $response = $this->getJson($this->uri(99999));

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
}
