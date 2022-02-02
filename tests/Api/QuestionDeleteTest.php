<?php

namespace EscolaLms\Questionnaire\Tests\Api;

use EscolaLms\Questionnaire\Models\Question;
use EscolaLms\Questionnaire\Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class QuestionDeleteTest extends TestCase
{
    use DatabaseTransactions;

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
