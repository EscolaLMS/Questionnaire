<?php

namespace EscolaLms\Questionnaire\Tests\Api;

use EscolaLms\Questionnaire\Models\QuestionnaireModelType;
use EscolaLms\Questionnaire\Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class QuestionnaireModelTypeTest extends TestCase
{
    use DatabaseTransactions;

    public function testAdminCanListEmptyQuestionnaireModels(): void
    {
        $this->authenticateAsAdmin();

        $response = $this->actingAs($this->user, 'api')->getJson('/api/admin/questionnaire-models');

        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'data',
            'message'
        ]);
        $response->assertJsonCount(0, 'data');
    }

    public function testCanReadQuestionnaireModels(): void
    {
        $this->authenticateAsAdmin();

        QuestionnaireModelType::factory()->createOne();

        $response = $this->actingAs($this->user, 'api')->getJson('/api/admin/questionnaire-models');
        $response->assertOk();
        $response->assertJsonCount(1, 'data');
    }

    public function testAnonymousCantListEmptyQuestionnaire(): void
    {
        $response = $this->getJson('/api/admin/questionnaire-models');

        $response->assertUnauthorized();
    }
}
