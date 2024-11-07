<?php

namespace EscolaLms\Questionnaire\Services\Contracts;

use EscolaLms\Questionnaire\Dtos\QuestionnaireModelDto;
use EscolaLms\Questionnaire\Models\QuestionnaireModel;
use EscolaLms\Questionnaire\Models\QuestionnaireModelType;
use Illuminate\Support\Collection;

/**
 * Interface QuestionnaireModelServiceContract
 * @package EscolaLms\Questionnaire\Http\Services\Contracts
 */
interface QuestionnaireModelServiceContract
{
    public function deleteQuestionnaireModel(QuestionnaireModel $questionnaireModel): bool;
    public function saveModelsForQuestionnaire(int $questionnaireId, array $models): void;
    public function assign(QuestionnaireModelType $questionnaireModelType, QuestionnaireModelDto $dto): QuestionnaireModel;
    public function unassign(QuestionnaireModelType $questionnaireModelType, QuestionnaireModelDto $dto): void;
    public function getQuestionnaireDataToExport(QuestionnaireModelDto $dto, QuestionnaireModelType $questionnaireModelType): Collection;
}
