<?php

namespace EscolaLms\Questionnaire\Tests\Api;

use EscolaLms\Questionnaire\Models\Question;
use EscolaLms\Questionnaire\Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class QuestionReadTest extends TestCase
{
    use DatabaseTransactions;

    private function uri(int $id): string
    {
        return sprintf('/api/admin/question/%d', $id);
    }

    /*public function testCannotFindMissingQuestion(): void
    {
        $response = $this->getJson($this->uri(99999));
        $response->assertNotFound();
    }*/

    public function testAdminCanReadExistingQuestionById(): void
    {
        $this->authenticateAsAdmin();

        $question = Question::factory()->createOne();

        $response = $this->actingAs($this->user, 'api')->getJson('/api/admin/question/' . $question->getKey());
        $response->assertOk();
        $response->assertJsonFragment(collect($question->getAttributes())->except('id')->toArray());
    }
}
