<?php

namespace EscolaLms\Questionnaire\Services\Contracts;

use EscolaLms\Core\Dtos\OrderDto;
use EscolaLms\Core\Models\User;
use EscolaLms\Questionnaire\Models\Questionnaire;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Interface QuestionnaireServiceContract
 * @package EscolaLms\Questionnaire\Http\Services\Contracts
 */
interface QuestionnaireServiceContract
{
    public function deleteQuestionnaire(Questionnaire $questionnaire): bool;

    public function searchForFront(array $filters, User $user, ?OrderDto $orderDto = null): LengthAwarePaginator;

    public function list(array $criteria, OrderDto $orderDto): LengthAwarePaginator;

    public function findForFront(array $filters, User $user): ?array;

    public function answer(array $params, User $user): bool;

    public function createQuestionnaire(array $data): Questionnaire;

    public function updateQuestionnaire(Questionnaire $questionnaire, array $data): Questionnaire;
}
