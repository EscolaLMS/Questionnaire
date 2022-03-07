<?php

namespace EscolaLms\Questionnaire\Services\Contracts;

use EscolaLms\Core\Models\User;
use EscolaLms\Questionnaire\Models\QuestionnaireModel;
use Illuminate\Support\Collection;

/**
 * Interface QuestionnaireAnswerServiceContract
 * @package EscolaLms\Questionnaire\Http\Services\Contracts
 */
interface QuestionnaireAnswerServiceContract
{
    public function getReport(int $id, ?int $model_type_id = null, ?int $model_id = null, ?int $user_id = null): Collection;

    public function getStars(int $model_type_id, int $model_id): Collection;

    public function saveAnswers(QuestionnaireModel $questionnaireModel, array $data, User $user): ?array;
}
