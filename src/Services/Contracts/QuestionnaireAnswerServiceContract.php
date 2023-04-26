<?php

namespace EscolaLms\Questionnaire\Services\Contracts;

use EscolaLms\Core\Models\User;
use EscolaLms\Questionnaire\Models\QuestionAnswer;
use EscolaLms\Questionnaire\Models\QuestionnaireModel;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Interface QuestionnaireAnswerServiceContract
 * @package EscolaLms\Questionnaire\Http\Services\Contracts
 */
interface QuestionnaireAnswerServiceContract
{
    public function getReport(int $id, ?int $modelTypeId = null, ?int $modelId = null): Collection;

    public function getStars(int $modelTypeId, int $modelId): array;

    public function saveAnswer(QuestionnaireModel $questionnaireModel, array $data, User $user): ?array;
    public function publicQuestionAnswers(array $criteria): LengthAwarePaginator;
    public function getReviewStars(array $criteria): array;
    public function searchAndPaginate(array $search = [], ?int $perPage = null, string $orderDirection = 'asc', string $orderColumn = 'id'): LengthAwarePaginator;
    public function update(array $input, int $id): QuestionAnswer;
}
