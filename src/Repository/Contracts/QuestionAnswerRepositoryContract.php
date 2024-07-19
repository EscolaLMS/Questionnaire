<?php

namespace EscolaLms\Questionnaire\Repository\Contracts;

use EscolaLms\Core\Dtos\OrderDto;
use EscolaLms\Core\Repositories\Contracts\BaseRepositoryContract;
use EscolaLms\Questionnaire\Dtos\QuestionAnswerFilterCriteriaDto;
use EscolaLms\Questionnaire\Models\QuestionAnswer;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface QuestionAnswerRepositoryContract extends BaseRepositoryContract
{
    public function getReport(
        int $questionnaireId,
        ?int $modelTypeId = null,
        ?int $modelId = null
    ): Collection;

    public function getStars(int $modelTypeId, int $modelId): Collection;

    public function deleteByModelId(int $modelId): bool;
    public function deleteByQuestionId(int $questionId): bool;
    public function searchAndPaginate(array $search = [], ?int $perPage = null, string $orderDirection = 'asc', string $orderColumn = 'id'): LengthAwarePaginator;
    public function listWithCriteriaAndOrder(int $questionnaireId, QuestionAnswerFilterCriteriaDto $criteriaDto, OrderDto $orderDto, int $perPage): LengthAwarePaginator;
    public function updateOrCreate(array $attributes, array $values): QuestionAnswer;
    public function findAnswer(int $userId, int $questionId, int $questionnaireModelId): ?QuestionAnswer;
    public function searchByCriteriaWithPagination(array $criteria, ?int $perPage = null): LengthAwarePaginator;
    public function getReviewReport(array $criteria): QuestionAnswer;
}
