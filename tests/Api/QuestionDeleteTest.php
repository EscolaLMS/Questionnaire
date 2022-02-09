<?php

namespace EscolaLms\Questionnaire\Tests\Api;

use EscolaLms\Questionnaire\Database\Seeders\QuestionnairePermissionsSeeder;
use EscolaLms\Questionnaire\Models\Question;
use EscolaLms\Questionnaire\Models\QuestionAnswer;
use EscolaLms\Questionnaire\Models\QuestionnaireModel;
use EscolaLms\Questionnaire\Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class QuestionDeleteTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(QuestionnairePermissionsSeeder::class);
    }

    private function uri(int $id): string
    {
        return sprintf('/api/admin/question/%d', $id);
    }

    public function testAdminCanDeleteExistingQuestion(): void
    {
        $this->authenticateAsAdmin();

        $question = Question::factory()->createOne();
        $response = $this->actingAs($this->user, 'api')->delete($this->uri($question->id));
        $response->assertOk();
        $this->assertEquals(0, Question::where('id', $question->id)->count());
    }

    public function testAdminCanDeleteExistingQuestionWithAllAnswers(): void
    {
        $this->authenticateAsAdmin();
        $question = Question::factory()->createOne();
        QuestionnaireModel::factory()->createOne();
        QuestionAnswer::factory()
            ->count(20)
            ->create();

        $this->assertEquals(1, Question::where('id', $question->id)->count());
        $this->assertEquals(20, QuestionAnswer::count());
        $this->assertEquals(1, QuestionnaireModel::count());

        $response = $this->actingAs($this->user, 'api')->delete($this->uri($question->id));

        $response->assertOk();
        $this->assertEquals(0, QuestionAnswer::count());
        $this->assertEquals(1, QuestionnaireModel::count());
        $this->assertEquals(0, Question::where('id', $question->id)->count());
    }

    public function testAdminCannotDeleteMissingQuestion(): void
    {
        $this->authenticateAsAdmin();

        $question = Question::factory()->makeOne();
        $question->id = 999999;

        $response = $this->actingAs($this->user, 'api')->delete($this->uri($question->id));

        $response->assertStatus(404);
    }

    public function testGuestCannotDeleteExistingQuestion(): void
    {
        $question = Question::factory()->createOne();
        $response = $this->json('delete', $this->uri($question->id));
        $response->assertUnauthorized();
    }
}
