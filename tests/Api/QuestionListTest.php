<?php

namespace EscolaLms\Questionnaire\Tests\Api;

use EscolaLms\Questionnaire\Models\Question;
use EscolaLms\Questionnaire\Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class QuestionListTest extends TestCase
{
    use DatabaseTransactions;

    public function testAdminCanListEmptyQuestion(): void
    {
        $this->authenticateAsAdmin();

        $response = $this->actingAs($this->user, 'api')->getJson('/api/admin/question');
        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'data',
            'meta',
            'message'
        ]);
        $response->assertJsonCount(0, 'data');
    }

    public function testAdminCanListQuestion(): void
    {
        $this->authenticateAsAdmin();

        $questions = Question::factory()
            ->count(10)
            ->create();

        $questionsArr = $questions->map(function (Question $p) {
            return $p->toArray();
        })->toArray();

        $response = $this->actingAs($this->user, 'api')->getJson('/api/admin/question');
        $response->assertOk();
        $response->assertJsonFragment(
            $questionsArr[0],
        );
    }
}
