<?php

namespace EscolaLms\Questionnaire\Tests\Api;

use EscolaLms\Questionnaire\Database\Seeders\QuestionnairePermissionsSeeder;
use EscolaLms\Questionnaire\Enums\QuestionTypeEnum;
use EscolaLms\Questionnaire\Models\Question;
use EscolaLms\Questionnaire\Models\Questionnaire;
use EscolaLms\Questionnaire\Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class QuestionListTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(QuestionnairePermissionsSeeder::class);
    }

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

    public function testListWithFiltersAndSort(): void
    {
        $this->authenticateAsAdmin();

        $questionnaire1 = Questionnaire::factory()->create();
        $questionnaire2 = Questionnaire::factory()->create();

        $question1 = Question::factory()
            ->create([
                'title' => 'aaaa',
                'questionnaire_id' => $questionnaire1->getKey(),
                'type' => QuestionTypeEnum::RATE,
                'description' => 'aaaaa',
                'position' => 1,
            ]);

        $question2 = Question::factory()
            ->create([
                'title' => 'bbbb',
                'questionnaire_id' => $questionnaire1->getKey(),
                'type' => QuestionTypeEnum::TEXT,
                'description' => 'bbbbb',
                'position' => 2,
            ]);

        $question3 = Question::factory()
            ->create([
                'title' => 'cccc',
                'questionnaire_id' => $questionnaire2->getKey(),
            ]);

        $question4 = Question::factory()
            ->create([
                'title' => 'dddd',
                'questionnaire_id' => $questionnaire2->getKey(),
            ]);

        $response = $this->actingAs($this->user, 'api')->json('GET', '/api/admin/question', [
            'title' => 'aaa',
            'questionnaire_id' => $questionnaire1->getKey(),
        ]);

        $response->assertJsonCount(1, 'data');
        $this->assertTrue($response->json('data.0.id') === $question1->getKey());

        $response = $this->actingAs($this->user, 'api')->json('GET', '/api/admin/question', [
            'order_by' => 'title',
            'order' => 'ASC',
            'questionnaire_id' => $questionnaire1->getKey(),
        ]);

        $response->assertJsonCount(2, 'data');
        $this->assertTrue($response->json('data.0.id') === $question1->getKey());
        $this->assertTrue($response->json('data.1.id') === $question2->getKey());

        $response = $this->actingAs($this->user, 'api')->json('GET', '/api/admin/question', [
            'order_by' => 'title',
            'order' => 'DESC',
            'questionnaire_id' => $questionnaire1->getKey(),
        ]);

        $response->assertJsonCount(2, 'data');
        $this->assertTrue($response->json('data.0.id') === $question2->getKey());
        $this->assertTrue($response->json('data.1.id') === $question1->getKey());

        $response = $this->actingAs($this->user, 'api')->json('GET', '/api/admin/question', [
            'order_by' => 'id',
            'order' => 'ASC',
            'questionnaire_id' => $questionnaire1->getKey(),
        ]);

        $this->assertTrue($response->json('data.0.id') === $question1->getKey());
        $this->assertTrue($response->json('data.1.id') === $question2->getKey());

        $response = $this->actingAs($this->user, 'api')->json('GET', '/api/admin/question', [
            'order_by' => 'id',
            'order' => 'DESC',
            'questionnaire_id' => $questionnaire1->getKey(),
        ]);

        $this->assertTrue($response->json('data.0.id') === $question2->getKey());
        $this->assertTrue($response->json('data.1.id') === $question1->getKey());

        $response = $this->actingAs($this->user, 'api')->json('GET', '/api/admin/question', [
            'order_by' => 'type',
            'order' => 'ASC',
            'questionnaire_id' => $questionnaire1->getKey(),
        ]);

        $this->assertTrue($response->json('data.0.id') === $question1->getKey());
        $this->assertTrue($response->json('data.1.id') === $question2->getKey());

        $response = $this->actingAs($this->user, 'api')->json('GET', '/api/admin/question', [
            'order_by' => 'type',
            'order' => 'DESC',
            'questionnaire_id' => $questionnaire1->getKey(),
        ]);

        $this->assertTrue($response->json('data.0.id') === $question2->getKey());
        $this->assertTrue($response->json('data.1.id') === $question1->getKey());

        $response = $this->actingAs($this->user, 'api')->json('GET', '/api/admin/question', [
            'order_by' => 'description',
            'order' => 'ASC',
            'questionnaire_id' => $questionnaire1->getKey(),
        ]);

        $this->assertTrue($response->json('data.0.id') === $question1->getKey());
        $this->assertTrue($response->json('data.1.id') === $question2->getKey());

        $response = $this->actingAs($this->user, 'api')->json('GET', '/api/admin/question', [
            'order_by' => 'description',
            'order' => 'DESC',
            'questionnaire_id' => $questionnaire1->getKey(),
        ]);

        $this->assertTrue($response->json('data.0.id') === $question2->getKey());
        $this->assertTrue($response->json('data.1.id') === $question1->getKey());

        $response = $this->actingAs($this->user, 'api')->json('GET', '/api/admin/question', [
            'order_by' => 'position',
            'order' => 'ASC',
            'questionnaire_id' => $questionnaire1->getKey(),
        ]);

        $this->assertTrue($response->json('data.0.id') === $question1->getKey());
        $this->assertTrue($response->json('data.1.id') === $question2->getKey());

        $response = $this->actingAs($this->user, 'api')->json('GET', '/api/admin/question', [
            'order_by' => 'position',
            'order' => 'DESC',
            'questionnaire_id' => $questionnaire1->getKey(),
        ]);

        $this->assertTrue($response->json('data.0.id') === $question2->getKey());
        $this->assertTrue($response->json('data.1.id') === $question1->getKey());
    }
}
