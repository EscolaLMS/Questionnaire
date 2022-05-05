<?php

namespace EscolaLms\Questionnaire\Services;

use EscolaLms\Core\Models\User;
use EscolaLms\Questionnaire\Models\QuestionAnswer;
use EscolaLms\Questionnaire\Models\QuestionnaireModel;
use EscolaLms\Questionnaire\Repository\Contracts\QuestionAnswerRepositoryContract;
use EscolaLms\Questionnaire\Services\Contracts\QuestionnaireAnswerServiceContract;
use EscolaLms\Questionnaire\Services\Contracts\QuestionnaireServiceContract;
use Illuminate\Support\Collection;

class QuestionnaireAnswerService implements QuestionnaireAnswerServiceContract
{
    private QuestionAnswerRepositoryContract $questionAnswerRepository;
    private QuestionnaireServiceContract $questionnaireService;

    public function __construct(
        QuestionAnswerRepositoryContract $questionAnswerRepository,
        QuestionnaireServiceContract $questionnaireService
    ) {
        $this->questionAnswerRepository = $questionAnswerRepository;
        $this->questionnaireService = $questionnaireService;
    }

    public function getReport(int $id, ?int $modelTypeId = null, ?int $modelId = null): Collection
    {
        $report = $this->questionAnswerRepository->getReport($id, $modelTypeId, $modelId)->toArray();

        return new collection($report);
    }

    public function getStars(int $modelTypeId, int $modelId): Collection
    {
        $report = $this->questionAnswerRepository->getStars($modelTypeId, $modelId)->toArray();

        return new collection($report);
    }

    public function saveAnswer(QuestionnaireModel $questionnaireModel, array $data, User $user): ?array
    {
        $this->questionAnswerRepository->updateOrCreate(
            [
                'user_id' => $user->getKey(),
                'question_id' => $data['question_id'],
                'questionnaire_model_id' => $questionnaireModel->getKey(),
            ],
            [
                'rate' => $data['rate'] ?? null,
                'note' => $data['note'] ?? null,
            ]
        );

        return $this->questionnaireService->findForFront(
            [
                'id' => $questionnaireModel->questionnaire_id,
                'model_type_title' => $questionnaireModel->modelableType->title,
                'model_id' => $questionnaireModel->model_id,
            ],
            $user
        );
    }
}
