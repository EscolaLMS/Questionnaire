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

    public Questionnaire $questionnaire;
    public QuestionnaireModel $questionnaireModel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authenticateAsAdmin();

        $this->questionnaire = Questionnaire::factory()->createOne();
        $this->questionnaireModel = QuestionnaireModel::factory()->createOne();

        Question::factory()
            ->count(20)
            ->create();

        QuestionAnswer::factory()
            ->count(200)
            ->create();
    }

    public function testCanReadQuestionnaireReport(): void
    {
        $response = $this->actingAs($this->user, 'api')->getJson(
            sprintf('/api/admin/questionnaire/report/%d', $this->questionnaire->id)
        );

        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'data',
            'message'
        ]);
        $response->assertJsonCount(20, 'data');
    }

    public function testCanReadQuestionnaireReportWithAllParams(): void
    {
        $response = $this->actingAs($this->user, 'api')->getJson(
            sprintf(
                '/api/admin/questionnaire/report/%d/%d/%d/%d',
                $this->questionnaire->id,
                $this->questionnaireModel->modelable_type_id,
                $this->questionnaireModel->modelable_id,
                $this->user->id
            )
        );

        $response->assertOk();
    }
}
