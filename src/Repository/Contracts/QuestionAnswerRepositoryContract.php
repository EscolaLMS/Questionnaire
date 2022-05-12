<?php

namespace EscolaLms\Questionnaire\Repository\Contracts;

use EscolaLms\Core\Repositories\Contracts\BaseRepositoryContract;
use EscolaLms\Questionnaire\Models\QuestionAnswer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

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
    public function updateOrCreate(array $attributes, array $values): QuestionAnswer;
}
