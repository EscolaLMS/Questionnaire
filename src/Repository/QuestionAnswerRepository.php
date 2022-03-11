<?php

namespace EscolaLms\Questionnaire\Repository;

use EscolaLms\Core\Repositories\BaseRepository;
use EscolaLms\Questionnaire\Enums\QuestionTypeEnum;
use EscolaLms\Questionnaire\Models\Question;
use EscolaLms\Questionnaire\Models\QuestionAnswer;
use EscolaLms\Questionnaire\Repository\Contracts\QuestionAnswerRepositoryContract;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

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

    public function insert(Question $question): Question
    {
        return $this->createUsingModel($question);
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
            ->selectRaw('SUM(rate) as sum_rate, COUNT(rate) as count_answers, AVG(rate) as avg_rate')
            ->groupBy('questions.questionnaire_id');

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
            ->select($this->model->table.'.*')
            ->orderBy($this->model->table.'.'.$orderColumn, $orderDirection)
            ->join('questions', 'question_id', '=', 'questions.id')
            ->where('questions.questionnaire_id', '=', $search['questionnaire_id'])
            ->paginate($perPage);
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
            ->where('questions.type', '=', QuestionTypeEnum::RATE)
            ->when($questionnaireId, fn ($q) => $q->where('questions.questionnaire_id', '=', $questionnaireId))
            ->when($modelTypeId, fn ($q) => $q
                    ->join('questionnaire_models', 'questionnaire_models.id', '=', 'questionnaire_model_id')
                    ->where('questionnaire_models.model_type_id', '=', $modelTypeId)
                    ->when($modelId, fn ($q) => $q->where('questionnaire_models.model_id', '=', $modelId))
            );
    }
}
