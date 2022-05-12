<?php

namespace EscolaLms\Questionnaire\Services;

use EscolaLms\Core\Models\User;
use EscolaLms\Questionnaire\Enums\QuestionnaireRateMap;
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

    public function getStars(int $modelTypeId, int $modelId): array
    {
        $report = $this->questionAnswerRepository->getStars($modelTypeId, $modelId);
        $countRates = 0;
        $sumRates = 0;
        $rateMap = QuestionnaireRateMap::RATE_MAP;
        foreach ($report as $rates) {
            if (isset($rateMap[$rates->rate])) {
                $rateMap[$rates->rate] = $rates->count_rate ?? 0;
                $sumRates += ($rates->rate * $rates->count_rate);
                $countRates += $rates->count_rate;
            }
        }
        return [
            'avg_rate' => $sumRates/max(1, $countRates),
            'count_answers' => $countRates,
            'sum_rates' => $sumRates,
            'rates' => $rateMap
        ];
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
