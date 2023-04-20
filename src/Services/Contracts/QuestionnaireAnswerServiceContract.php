<?php

namespace EscolaLms\Questionnaire\Services\Contracts;

use EscolaLms\Core\Models\User;
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
}
