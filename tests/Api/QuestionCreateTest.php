<?php

namespace EscolaLms\Questionnaire\Tests\Api;

use EscolaLms\Questionnaire\Database\Seeders\QuestionnairePermissionsSeeder;
use EscolaLms\Questionnaire\Enums\QuestionTypeEnum;
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

    public function testCreateQuestionAlreadyExistReview(): void
    {
        $this->authenticateAsAdmin();
        $questionnaire = Questionnaire::factory()->createOne();
        Question::factory([
            'questionnaire_id' => $questionnaire->getKey(),
            'type' => QuestionTypeEnum::REVIEW,
        ])->createOne();

        $this
            ->actingAs($this->user, 'api')
            ->json(
                'POST',
                '/api/admin/question',
                [
                    'title' => 'Opinia',
                    'questionnaire_id' => $questionnaire->getKey(),
                    'description' => 'opis',
                    'position' => 0,
                    'active' => true,
                    'type' => QuestionTypeEnum::REVIEW,
                    'public_answers' => true,
                ]
            )
            ->assertUnprocessable();
    }
}
