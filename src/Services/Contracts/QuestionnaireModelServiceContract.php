<?php

namespace EscolaLms\Questionnaire\Services\Contracts;

use EscolaLms\Questionnaire\Models\QuestionnaireModel;

/**
 * Interface QuestionnaireModelServiceContract
 * @package EscolaLms\Questionnaire\Http\Services\Contracts
 */
interface QuestionnaireModelServiceContract
{
    public function deleteQuestionnaireModel(QuestionnaireModel $questionnaireModel): bool;
    public function saveModelsForQuestionnaire(int $questionnaireId, array $models): void;
}
