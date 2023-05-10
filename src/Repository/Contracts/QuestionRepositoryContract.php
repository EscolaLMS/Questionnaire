<?php

namespace EscolaLms\Questionnaire\Repository\Contracts;

use EscolaLms\Core\Dtos\OrderDto;
use EscolaLms\Core\Repositories\Contracts\BaseRepositoryContract;
use EscolaLms\Questionnaire\Dtos\QuestionFilterCriteriaDto;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface QuestionRepositoryContract extends BaseRepositoryContract
{
    public function searchAndPaginate(array $search = [], ?int $perPage = null, string $orderDirection = 'asc', string $orderColumn = 'id'): LengthAwarePaginator;

    public function listWithCriteriaAndOrder(QuestionFilterCriteriaDto $criteriaDto, OrderDto $orderDto, int $perPage): LengthAwarePaginator;
}
