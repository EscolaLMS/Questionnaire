<?php

namespace EscolaLms\Questionnaire\Tests\Api;

use EscolaLms\Questionnaire\Database\Seeders\QuestionnairePermissionsSeeder;
use EscolaLms\Questionnaire\Models\Question;
use EscolaLms\Questionnaire\Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class QuestionReadTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(QuestionnairePermissionsSeeder::class);
    }

    public function testAdminCanReadExistingQuestionById(): void
    {
        $this->authenticateAsAdmin();

        $question = Question::factory()->createOne();

        $response = $this->actingAs($this->user, 'api')->getJson('/api/admin/question/' . $question->getKey());
        $response->assertOk();
        $response->assertJsonFragment(collect($question->getAttributes())->except('id')->toArray());
    }
}
