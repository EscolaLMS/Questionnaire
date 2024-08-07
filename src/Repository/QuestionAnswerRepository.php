<?php

namespace EscolaLms\Questionnaire\Repository;

use EscolaLms\Core\Dtos\OrderDto;
use EscolaLms\Core\Repositories\BaseRepository;
use EscolaLms\Questionnaire\Dtos\QuestionAnswerFilterCriteriaDto;
use EscolaLms\Questionnaire\Enums\QuestionTypeEnum;
use EscolaLms\Questionnaire\EscolaLmsQuestionnaireServiceProvider;
use EscolaLms\Questionnaire\Models\Question;
use EscolaLms\Questionnaire\Models\QuestionAnswer;
use EscolaLms\Questionnaire\Repository\Contracts\QuestionAnswerRepositoryContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class QuestionAnswerRepository extends BaseRepository implements QuestionAnswerRepositoryContract
{
    public function model(): string
    {
        return QuestionAnswer::class;
    }

    public function getFieldsSearchable(): array
    {
        return [
            'id',
            'user_id',
            'question_id',
            'questionnaire_model_id',
            'rate',
            'note',
        ];
    }

    public function save(Question $question): bool
    {
        return $question->save();
    }

    public function getReport(
        int $questionnaireId,
        ?int $modelTypeId = null,
        ?int $modelId = null
    ): Collection {
        return $this->getQueryReport($questionnaireId, $modelTypeId, $modelId)
            ->selectRaw('SUM(rate) as sum_rate, COUNT(rate) as count_answers, AVG(rate) as avg_rate, question_id, questions.title')
            ->groupBy('question_id', 'questions.title')->get();
    }

    public function getStars(
        int $modelTypeId,
        int $modelId
    ): Collection {
        $query = $this->getQueryReport(null, $modelTypeId, $modelId)
            ->selectRaw('rate, COUNT(rate) as count_rate')
            ->groupBy('question_answers.rate', 'questions.questionnaire_id');
        return $query->get();
    }

    public function deleteByModelId(int $modelId): bool
    {
        return $this->model->newQuery()->where('questionnaire_model_id', '=', $modelId)->delete();
    }

    public function deleteByQuestionId(int $questionId): bool
    {
        return $this->model->newQuery()->where('question_id', '=', $questionId)->delete();
    }

    public function searchAndPaginate(array $search = [], ?int $perPage = null, string $orderDirection = 'asc', string $orderColumn = 'id'): LengthAwarePaginator
    {
        return $this
            ->allQuery($search)
            ->select($this->model->getTable() . '.*')
            ->orderBy($this->model->getTable() . '.'.$orderColumn, $orderDirection)
            ->join('questions', 'question_id', '=', 'questions.id')
            ->where('questions.questionnaire_id', '=', $search['questionnaire_id'])
            ->paginate($perPage);
    }

    public function listWithCriteriaAndOrder(int $questionnaireId, QuestionAnswerFilterCriteriaDto $criteriaDto, OrderDto $orderDto, int $perPage): LengthAwarePaginator
    {
        $query =  $this
            ->queryWithAppliedCriteria($criteriaDto->toArray())
            ->select($this->model->getTable() . '.*')
            ->join('questions', 'question_id', '=', 'questions.id')
            ->where('questions.questionnaire_id', '=', $questionnaireId);

        return $this
            ->orderBy($query, $orderDto->getOrderBy(), $orderDto->getOrder())
            ->paginate($perPage);
    }

    public function updateOrCreate(array $attributes, array $values): QuestionAnswer
    {
        /** @var QuestionAnswer $questionAnswer */
        $questionAnswer = $this->model->updateOrCreate($attributes, $values);
        return $questionAnswer;
    }

    public function findAnswer(int $userId, int $questionId, int $questionnaireModelId): ?QuestionAnswer
    {
        /** @var QuestionAnswer|null $questionAnswer */
        $questionAnswer = $this
            ->model
            ->newQuery()
            ->where('user_id', '=', $userId)
            ->where('question_id', '=', $questionId)
            ->where('questionnaire_model_id', '=', $questionnaireModelId)
            ->first();
        return $questionAnswer;
    }

    public function searchByCriteriaWithPagination(array $criteria, ?int $perPage = null): LengthAwarePaginator
    {
        $query = $this
            ->model
            ->newQuery();

        return $this
            ->applyCriteria($query, $criteria)
            ->paginate($perPage ?? config(EscolaLmsQuestionnaireServiceProvider::CONFIG_KEY . '.per_page', 15));
    }

    public function getReviewReport(array $criteria): QuestionAnswer
    {
        $query = $this->model->newQuery();
        /** @var QuestionAnswer $questionAnswer */
        $questionAnswer = $this
            ->applyCriteria($query, $criteria)
            ->selectRaw('SUM(rate) as sum_rate, COUNT(rate) as count_answers, AVG(rate) as avg_rate, question_id, COUNT(CASE WHEN visible_on_front = true THEN 1 END) as count_public_answers')
            ->groupBy('question_id')
            ->firstOrFail();

        return $questionAnswer;
    }

    private function orderBy(Builder $query, ?string $orderBy, ?string $order): Builder
    {
        if (is_null($orderBy)) {
            return $query;
        }

        return match ($orderBy) {
            'question_title' => $query->orderBy('questions.title', $order),
            default => $query->orderBy($orderBy, $order),
        };
    }

    private function getQueryReport(
        ?int $questionnaireId = null,
        ?int $modelTypeId = null,
        ?int $modelId = null
    ): Builder
    {
        return $this
            ->model
            ->newQuery()
            ->join('questions', 'question_id', '=', 'questions.id')
            ->where('questions.type', '=', QuestionTypeEnum::REVIEW)
            ->when($questionnaireId, fn ($q) => $q->where('questions.questionnaire_id', '=', $questionnaireId))
            ->when($modelTypeId, fn ($q) => $q
                ->join('questionnaire_models', 'questionnaire_models.id', '=', 'questionnaire_model_id')
                ->where('questionnaire_models.model_type_id', '=', $modelTypeId)
                ->when($modelId, fn ($q) => $q->where('questionnaire_models.model_id', '=', $modelId))
            );
    }
}
