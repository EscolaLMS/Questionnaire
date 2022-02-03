<?php

namespace EscolaLms\Questionnaire\Services;

use EscolaLms\Questionnaire\Models\Questionnaire;
use EscolaLms\Questionnaire\Repository\Contracts\QuestionnaireRepositoryContract;
use EscolaLms\Questionnaire\Services\Contracts\QuestionnaireModelServiceContract;
use EscolaLms\Questionnaire\Services\Contracts\QuestionnaireServiceContract;
use EscolaLms\Questionnaire\Services\Contracts\QuestionServiceContract;
use Illuminate\Support\Facades\DB;

class QuestionnaireService implements QuestionnaireServiceContract
{
    private QuestionnaireRepositoryContract $questionnaireRepository;
    private QuestionnaireModelServiceContract $questionnaireModelService;
    private QuestionServiceContract $questionService;

    public function __construct(
        QuestionnaireRepositoryContract $questionnaireRepository,
        QuestionnaireModelServiceContract $questionnaireModelService,
        QuestionServiceContract $questionService
    ) {
        $this->questionnaireRepository = $questionnaireRepository;
        $this->questionnaireModelService = $questionnaireModelService;
        $this->questionService = $questionService;
    }

    public function deleteQuestionnaire(Questionnaire $questionnaire): bool
    {
        try {
            DB::transaction(function () use ($questionnaire) {
                foreach ($questionnaire->questions as $question) {
                    $this->questionService->deleteQuestion($question);
                }
                foreach ($questionnaire->questionnaireModels as $questionnaireModels) {
                    $this->questionnaireModelService->deleteQuestionnaireModel($questionnaireModels);
                }
                $this->questionnaireRepository->delete($questionnaire->id);
            });

            return true;
        } catch (\Exception $err) {
            return false;
        }
    }
}
