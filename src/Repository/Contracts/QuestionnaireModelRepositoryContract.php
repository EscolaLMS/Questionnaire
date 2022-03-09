<?php

namespace EscolaLms\Questionnaire\Repository\Contracts;

use EscolaLms\Core\Repositories\Contracts\BaseRepositoryContract;
use EscolaLms\Questionnaire\Models\Questionnaire;
use EscolaLms\Questionnaire\Models\QuestionnaireModel;

interface QuestionnaireModelRepositoryContract extends BaseRepositoryContract
{
    public function findByModelTitleAndModelId(string $title, int $model_id): QuestionnaireModel;
    public function assignQuestionnaireModel(Questionnaire $questionnaire, int $modelTypeId, int $id): QuestionnaireModel;
    public function unassignQuestionnaireModel(Questionnaire $questionnaire, int $modelTypeId, int $modelId): bool;
}
