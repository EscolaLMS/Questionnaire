<?php

namespace EscolaLms\Questionnaire\Tests\Api;

use EscolaLms\Questionnaire\Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class QuestionnaireModelTypeTest extends TestCase
{
    use DatabaseTransactions;

    public function testCanReadQuestionnaireModels(): void
    {
        $this->authenticateAsAdmin();

        $response = $this->actingAs($this->user, 'api')->getJson('/api/admin/questionnaire-models');

        $response->assertOk();
    }
}
