<?php

namespace EscolaLms\Questionnaire\Repository\Contracts;

use EscolaLms\Core\Repositories\Contracts\BaseRepositoryContract;
use Illuminate\Database\Eloquent\Collection;

interface QuestionAnswerRepositoryContract extends BaseRepositoryContract
{
    public function getReport(
        int $questionnaireId,
        ?int $modelTypeId = null,
        ?int $modelId = null,
        ?int $userId = null
    ): Collection;
}
