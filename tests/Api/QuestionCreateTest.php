<?php

namespace EscolaLms\Questionnaire\Tests\Api;

use EscolaLms\Questionnaire\Database\Seeders\QuestionnairePermissionsSeeder;
use EscolaLms\Questionnaire\Models\Question;
use EscolaLms\Questionnaire\Models\Questionnaire;
use EscolaLms\Questionnaire\Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class QuestionCreateTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(QuestionnairePermissionsSeeder::class);
    }

    public function testAdminCanCreateQuestion(): void
    {
        $this->authenticateAsAdmin();
        $questionnaire = Questionnaire::factory()->create();;
        $question = Question::factory()->makeOne(['questionnaire_id' => $questionnaire->id]);

        $response = $this->actingAs($this->user, 'api')->postJson(
            '/api/admin/question',
            $question->toArray()
        );

        $response->assertStatus(201);

        $response2 = $this->actingAs($this->user, 'api')->getJson(
            '/api/admin/question/' . $question->id,
        );

        $response2->assertOk();
    }

    public function testAdminCannotCreateQuestionWithoutTitle(): void
    {
        $this->authenticateAsAdmin();

        $question = Question::factory()->makeOne();
        $response = $this->actingAs($this->user, 'api')->postJson(
            '/api/admin/question',
            collect($question->getAttributes())->except('id', 'title')->toArray()
        );
        $response->assertStatus(422);
    }

    public function testGuestCannotCreateQuestion(): void
    {
        $question = Question::factory()->makeOne();
        $response = $this->postJson(
            '/api/admin/question',
            collect($question->getAttributes())->except('id')->toArray()
        );
        $response->assertUnauthorized();
    }
}
