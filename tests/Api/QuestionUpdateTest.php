<?php

namespace EscolaLms\Questionnaire\Tests\Api;

use EscolaLms\Questionnaire\Database\Seeders\QuestionnairePermissionsSeeder;
use EscolaLms\Questionnaire\Enums\QuestionTypeEnum;
use EscolaLms\Questionnaire\Models\Question;
use EscolaLms\Questionnaire\Models\Questionnaire;
use EscolaLms\Questionnaire\Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class QuestionUpdateTest extends TestCase
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

    public function testAdminCanUpdateExistingQuestion(): void
    {
        $this->authenticateAsAdmin();

        $question = Question::factory()->createOne();
        $questionNew = Question::factory()->makeOne();

        $response = $this->actingAs($this->user, 'api')->patchJson(
            $this->uri($question->id),
            [
                'title' => $questionNew->title,
                'description' => $questionNew->description,
                'questionnaire_id' => $question->questionnaire_id,
            ]
        );
        $response->assertOk();
        $question->refresh();

        $this->assertEquals($questionNew->title, $question->title);
        $this->assertEquals($questionNew->description, $question->description);
    }

    public function testAdminCanUpdateExistingQuestionWithMissingTitle(): void
    {
        $this->authenticateAsAdmin();

        $question = Question::factory()->createOne();
        $questionNew = Question::factory()->makeOne();
        $oldTitle = $question->title;

        $response = $this->actingAs($this->user, 'api')->patchJson(
            $this->uri($question->id),
            [
                'description' => $questionNew->description,
                'questionnaire_id' => $question->questionnaire_id,
            ]
        );
        $response->assertStatus(200);
        $question->refresh();

        $this->assertEquals($oldTitle, $question->title);
        $this->assertEquals($questionNew->description, $question->description);
    }

    public function testAdminCanUpdateExistingQuestionWithMissingDescription(): void
    {
        $this->authenticateAsAdmin();

        $question = Question::factory()->createOne();
        $questionNew = Question::factory()->makeOne();
        $oldDescription = $question->description;

        $response = $this->actingAs($this->user, 'api')->patchJson(
            $this->uri($question->id),
            [
                'title' => $questionNew->title,
                'questionnaire_id' => $question->questionnaire_id,
            ]
        );
        $response->assertStatus(200);
        $question->refresh();

        $this->assertEquals($questionNew->title, $question->title);
        $this->assertEquals($oldDescription, $question->description);
    }

    public function testAdminCannotUpdateMissingQuestion(): void
    {
        $this->authenticateAsAdmin();

        $question = Question::factory()->makeOne();

        $response = $this->actingAs($this->user, 'api')->patchJson(
            $this->uri(99999999),
            [
                'title' => $question->title,
                'description' => $question->description,
                'questionnaire_id' => $question->questionnaire_id,
            ]
        );

        $response->assertStatus(404);
        $this->assertEquals(0, $question->newQuery()->where('id', $question->id)->count());
    }

    public function testGuestCannotUpdateExistingQuestion(): void
    {
        $question = Question::factory()->createOne();
        $questionNew = Question::factory()->makeOne();

        $oldTitle = $question->title;
        $oldDescription = $question->description;

        $response = $this->patchJson(
            $this->uri($question->id),
            [
                'title' => $questionNew->title,
                'description' => $questionNew->description,
                'questionnaire_id' => $question->questionnaire_id,
            ]
        );
        $response->assertUnauthorized();
        $question->refresh();

        $this->assertEquals($oldTitle, $question->title);
        $this->assertEquals($oldDescription, $question->description);
    }

    public function testUpdateQuestionReview(): void
    {
        $this->authenticateAsAdmin();
        $questionnaire = Questionnaire::factory()->createOne();

        $question = Question::factory([
            'questionnaire_id' => $questionnaire->getKey(),
            'type' => QuestionTypeEnum::RATE,
        ])->createOne();

        $this
            ->actingAs($this->user, 'api')
            ->json(
                'PATCH',
                $this->uri($question->getKey()),
                [
                    'title' => 'Opinia',
                    'questionnaire_id' => $questionnaire->getKey(),
                    'description' => 'opis',
                    'position' => 0,
                    'active' => true,
                    'type' => QuestionTypeEnum::REVIEW,
                    'public_answers' => true,
                    'max_score' => 10,
                ]
            )
            ->assertOk()
            ->assertJsonFragment([
                'max_score' => 10,
            ]);
    }

    public function testUpdateQuestionAlreadyExistReview(): void
    {
        $this->authenticateAsAdmin();
        $questionnaire = Questionnaire::factory()->createOne();
        Question::factory([
            'questionnaire_id' => $questionnaire->getKey(),
            'type' => QuestionTypeEnum::REVIEW,
        ])->createOne();

        $question = Question::factory([
            'questionnaire_id' => $questionnaire->getKey(),
            'type' => QuestionTypeEnum::RATE,
        ])->createOne();

        $this
            ->actingAs($this->user, 'api')
            ->json(
                'PATCH',
                $this->uri($question->getKey()),
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
