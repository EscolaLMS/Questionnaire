<?php

namespace EscolaLms\Questionnaire\Tests\Api;

use EscolaLms\Questionnaire\Models\Questionnaire;
use EscolaLms\Questionnaire\Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class QuestionnaireCreateTest extends TestCase
{
    use DatabaseTransactions;

    public function testAdminCanCreateQuestionnaire(): void
    {
        $this->authenticateAsAdmin();
        $questionnaire = Questionnaire::factory()->makeOne(['active' => false]);
        $response = $this->actingAs($this->user, 'api')->postJson(
            '/api/admin/questionnaire',
            $questionnaire->toArray()
        );

        $response->assertStatus(201);

        $response2 = $this->getJson(
            '/api/questionnaire/' . $questionnaire->id,
        );

        $response2->assertOk();

        $response3 = $this->actingAs($this->user, 'api')->getJson(
            '/api/admin/questionnaire/' . $questionnaire->id,
        );

        $response3->assertOk();
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
}
