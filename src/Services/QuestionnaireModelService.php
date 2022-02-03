<?php

namespace EscolaLms\Questionnaire\Services;

use EscolaLms\Questionnaire\Models\QuestionnaireModel;
use EscolaLms\Questionnaire\Repository\Contracts\QuestionAnswerRepositoryContract;
use EscolaLms\Questionnaire\Repository\Contracts\QuestionnaireModelRepositoryContract;
use EscolaLms\Questionnaire\Services\Contracts\QuestionnaireModelServiceContract;
use Illuminate\Support\Facades\DB;

class QuestionnaireModelService implements QuestionnaireModelServiceContract
{
    private QuestionAnswerRepositoryContract $questionAnswerRepository;
    private QuestionnaireModelRepositoryContract $questionnaireModelRepository;

    public function __construct(
        QuestionAnswerRepositoryContract $questionAnswerRepository,
        QuestionnaireModelRepositoryContract $questionnaireModelRepository
    ) {
        $this->questionAnswerRepository = $questionAnswerRepository;
        $this->questionnaireModelRepository = $questionnaireModelRepository;
    }

    public function deleteQuestionnaireModel(QuestionnaireModel $questionnaireModel): bool
    {
        try {
            DB::transaction(function () use ($questionnaireModel) {
                $this->questionAnswerRepository->deleteByModelId($questionnaireModel->id);
                $this->questionnaireModelRepository->delete($questionnaireModel->id);
            });

            return true;
        } catch (\Exception $err) {
            return false;
        }
    }
}
