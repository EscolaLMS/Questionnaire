<?php

namespace EscolaLms\Questionnaire\Repository;

use EscolaLms\Core\Dtos\OrderDto;
use EscolaLms\Core\Repositories\BaseRepository;
use EscolaLms\Questionnaire\Dtos\QuestionFilterCriteriaDto;
use EscolaLms\Questionnaire\Models\Question;
use EscolaLms\Questionnaire\Repository\Contracts\QuestionRepositoryContract;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class QuestionRepository extends BaseRepository implements QuestionRepositoryContract
{
    public function model(): string
    {
        return Question::class;
    }

    public function getFieldsSearchable(): array
    {
        return [
            'id',
            'description',
            'title',
            'questionnaire_id',
            'active',
            'type',
        ];
    }

    public function searchAndPaginate(array $search = [], ?int $perPage = null, string $orderDirection = 'asc', string $orderColumn = 'id'): LengthAwarePaginator
    {
        return $this->allQuery($search)->orderBy($orderColumn, $orderDirection)->paginate($perPage);
    }

    public function listWithCriteriaAndOrder(QuestionFilterCriteriaDto $criteriaDto, OrderDto $orderDto, int $perPage): LengthAwarePaginator
    {
        return $this
            ->queryWithAppliedCriteria($criteriaDto->toArray())
            ->orderBy($orderDto->getOrderBy() ?? 'id', $orderDto->getOrder() ?? 'asc')
            ->paginate($perPage);
    }

    public function save(Question $question): bool
    {
        return $question->save();
    }

    public function getAllQuestionnaireQuestions(int $id): Collection
    {
        return $this->model->newQuery()->where('questionnaire_id', '=', $id)->get();
    }
}
