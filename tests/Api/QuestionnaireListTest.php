<?php

namespace EscolaLms\Questionnaire\Tests\Api;

use EscolaLms\Questionnaire\Models\Questionnaire;
use EscolaLms\Questionnaire\Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class QuestionnaireListTest extends TestCase
{
    use DatabaseTransactions;

    public function testAdminCanListEmptyQuestionnaire(): void
    {
        $this->authenticateAsAdmin();

        $response = $this->actingAs($this->user, 'api')->getJson('/api/admin/questionnaire');

        $response->assertOk();

        $response->assertJsonStructure([
            'success',
            'data',
            'meta',
            'message'
        ]);

        $response->assertJsonCount(0, 'data');
    }

    public function testAdminCanListQuestionnaire(): void
    {
        $this->authenticateAsAdmin();

        $questionnaires = Questionnaire::factory()
            ->count(10)
            ->create();

        $questionnairesArr = $questionnaires->map(function (Questionnaire $p) {
            return $p->toArray();
        })->toArray();

        $response = $this->actingAs($this->user, 'api')->getJson('/api/admin/questionnaire');
        $response->assertOk();
        $response->assertJsonFragment(
            $questionnairesArr[0],
        );
    }

    public function testAnonymousCantListEmptyQuestionnaire(): void
    {
        $response = $this->getJson('/api/questionnaire');

        $response->assertForbidden();
    }

    public function testAnonymousCantListQuestionnaire(): void
    {
        Questionnaire::factory()
            ->count(10)
            ->create(['active' => true]);

        $response = $this->getJson('/api/questionnaire');
        $response->assertForbidden();
    }
}
