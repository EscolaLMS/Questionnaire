<?php

namespace EscolaLms\Questionnaire\Services\Contracts;

use EscolaLms\Core\Models\User;
use EscolaLms\Questionnaire\Models\Questionnaire;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Interface QuestionnaireServiceContract
 * @package EscolaLms\Questionnaire\Http\Services\Contracts
 */
interface QuestionnaireServiceContract
{
    public function deleteQuestionnaire(Questionnaire $questionnaire): bool;

    public function searchForFront(array $filters, User $user): LengthAwarePaginator;

    public function findForFront(array $filters, User $user): ?array;

    public function answer(array $params, User $user): bool;
}
