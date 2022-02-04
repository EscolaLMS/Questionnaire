<?php

namespace EscolaLms\Questionnaire\Repository\Contracts;

use EscolaLms\Core\Repositories\Contracts\BaseRepositoryContract;
use EscolaLms\Questionnaire\Models\QuestionnaireModel;

interface QuestionnaireModelRepositoryContract extends BaseRepositoryContract
{
    public function findByModelTitleAndModelId(string $title, int $model_id): QuestionnaireModel;
}
