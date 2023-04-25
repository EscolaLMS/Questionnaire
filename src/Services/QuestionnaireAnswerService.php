<?php

namespace EscolaLms\Questionnaire\Services;

use EscolaLms\Core\Models\User;
use EscolaLms\Core\Repositories\Criteria\Primitives\WhereCriterion;
use EscolaLms\Questionnaire\Enums\QuestionnaireRateMap;
use EscolaLms\Questionnaire\Enums\QuestionTypeEnum;
use EscolaLms\Questionnaire\EscolaLmsQuestionnaireServiceProvider;
use EscolaLms\Questionnaire\Models\QuestionnaireModel;
use EscolaLms\Questionnaire\Repository\Contracts\QuestionAnswerRepositoryContract;
use EscolaLms\Questionnaire\Repository\Contracts\QuestionRepositoryContract;
use EscolaLms\Questionnaire\Repository\Criteria\AnswerQuestionReviewCriterion;
use EscolaLms\Questionnaire\Services\Contracts\QuestionnaireAnswerServiceContract;
use EscolaLms\Questionnaire\Services\Contracts\QuestionnaireServiceContract;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;

class QuestionnaireAnswerService implements QuestionnaireAnswerServiceContract
{
    private QuestionAnswerRepositoryContract $questionAnswerRepository;
    private QuestionnaireServiceContract $questionnaireService;
    private QuestionRepositoryContract $questionRepository;

    public function __construct(
        QuestionAnswerRepositoryContract $questionAnswerRepository,
        QuestionnaireServiceContract $questionnaireService,
        QuestionRepositoryContract $questionRepository
    ) {
        $this->questionAnswerRepository = $questionAnswerRepository;
        $this->questionnaireService = $questionnaireService;
        $this->questionRepository = $questionRepository;
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
        $report->each(function ($rates) use(&$rateMap, &$sumRates, &$countRates) {
            if (isset($rateMap[$rates->rate])) {
                $rateMap[$rates->rate] += $rates->count_rate ?? 0;
                $sumRates += ($rates->rate * $rates->count_rate);
                $countRates += $rates->count_rate;
            }
        });
        return [
            'avg_rate' => $sumRates/max(1, $countRates),
            'count_answers' => $countRates,
            'sum_rates' => $sumRates,
            'rates' => $rateMap
        ];
    }

    public function saveAnswer(QuestionnaireModel $questionnaireModel, array $data, User $user): ?array
    {
        $questionId = $data['question_id'];
        $answer = $this->questionAnswerRepository->findAnswer($user->getKey(), $questionId, $questionnaireModel->getKey());
        if (is_null($answer)) {
            $question = $this->questionRepository->find($questionId);
            $public = $question && !$question->public_answers
                ? false
                : Config::get(EscolaLmsQuestionnaireServiceProvider::CONFIG_KEY . '.new_answers_visible_by_default', false);
            $this
                ->questionAnswerRepository
                ->create(
                    array_merge(
                        $data,
                        [
                            'visible_on_front' => $public,
                            'user_id' => $user->getKey(),
                            'question_id' => $questionId,
                            'questionnaire_model_id' => $questionnaireModel->getKey(),
                        ]
                    )
                );
        } else {
            $this->questionAnswerRepository->update([
                'rate' => $data['rate'] ?? null,
                'note' => $data['note'] ?? null,
            ], $answer->getKey());
        }

        return $this->questionnaireService->findForFront(
            [
                'questionnaire_id' => $questionnaireModel->questionnaire_id,
                'model_type_title' => $questionnaireModel->modelableType->title,
                'model_id' => $questionnaireModel->model_id,
            ],
            $user
        );
    }

    public function publicQuestionAnswers(array $criteria): LengthAwarePaginator
    {
        $criteria[] = new WhereCriterion('visible_on_front', true, '=');
        return $this->questionAnswerRepository->searchByCriteriaWithPagination($criteria);
    }

    public function getReviewStars(array $criteria): array
    {
        $criteria[] = new AnswerQuestionReviewCriterion(null, QuestionTypeEnum::REVIEW);
        return $this->questionAnswerRepository->getReviewReport($criteria)->toArray();
    }
}
