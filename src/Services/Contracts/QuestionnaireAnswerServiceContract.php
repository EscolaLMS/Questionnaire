<?php

namespace EscolaLms\Questionnaire\Services\Contracts;

use Illuminate\Support\Collection;

/**
 * Interface QuestionnaireAnswerServiceContract
 * @package EscolaLms\Questionnaire\Http\Services\Contracts
 */
interface QuestionnaireAnswerServiceContract
{
    public function getReport(int $id, ?int $model_type_id = null, ?int $model_id = null, ?int $user_id = null): Collection;
}
