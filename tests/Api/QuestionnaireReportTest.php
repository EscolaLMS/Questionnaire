<?php

namespace EscolaLms\Questionnaire\Tests\Api;

use EscolaLms\Questionnaire\Http\Resources\QuestionnaireModelResource;
use EscolaLms\Questionnaire\Models\Question;
use EscolaLms\Questionnaire\Models\QuestionAnswer;
use EscolaLms\Questionnaire\Models\Questionnaire;
use EscolaLms\Questionnaire\Models\QuestionnaireModel;
use EscolaLms\Questionnaire\Tests\TestCase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class QuestionnaireReportTest extends TestCase
{
    use DatabaseTransactions;

    public Model $questionnaire;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authenticateAsAdmin();

        $this->questionnaire = Questionnaire::factory()->createOne();
        QuestionnaireModel::factory()->createOne();
        Questionnaire::factory()
            ->count(2)
            ->create();

        Question::factory()
            ->count(20)
            ->create();

        QuestionAnswer::factory()
            ->count(20)
            ->create();
    }

    public function testCanReadQuestionnaireReport(): void
    {
        $response = $this->actingAs($this->user, 'api')->getJson(
            sprintf('/api/admin/questionnaire/report/%d', $this->questionnaire->id)
        );

        $response->assertOk();
    }
}
