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

    public function testAnonymousCanListEmptyQuestionnaire(): void
    {
        $this->authenticateAsAdmin();

        $response = $this->getJson('/api/questionnaire');
        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'data',
            'meta',
            'message'
        ]);
        $response->assertJsonCount(0, 'data');
    }

    public function testAnonymousCanListQuestionnaire(): void
    {
        $this->authenticateAsAdmin();

        $questionnaires = Questionnaire::factory()
            ->count(10)
            ->create(['active' => true]);

        $questionnairesArr = $questionnaires->map(function (Questionnaire $p) {
            return $p->toArray();
        })->values()->toArray();


        $response = $this->getJson('/api/questionnaire');
        $response->assertOk();
        $response->assertJsonFragment(
            $questionnairesArr[0]
        );
    }
}
