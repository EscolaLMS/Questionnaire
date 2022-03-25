<?php

namespace EscolaLms\Questionnaire\Tests\Api;

use EscolaLms\Questionnaire\Database\Seeders\QuestionnairePermissionsSeeder;
use EscolaLms\Questionnaire\Models\QuestionnaireModelType;
use EscolaLms\Questionnaire\Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class QuestionnaireModelTypeTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(QuestionnairePermissionsSeeder::class);
    }

    public function testCanReadQuestionnaireModels(): void
    {
        $this->authenticateAsAdmin();

        $questionnaireModelType = QuestionnaireModelType::query()->inRandomOrder()->first();
        if (empty($questionnaireModelType)) {
            QuestionnaireModelType::factory()->createOne();
        }

        $count = QuestionnaireModelType::query()->count();

        $response = $this->actingAs($this->user, 'api')->getJson('/api/admin/questionnaire-models');
        $response->assertOk();
        $response->assertJsonCount($count, 'data');
    }

    public function testAnonymousCantListEmptyQuestionnaire(): void
    {
        $response = $this->getJson('/api/admin/questionnaire-models');

        $response->assertUnauthorized();
    }
}
