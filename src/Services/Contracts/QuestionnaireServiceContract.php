<?php

namespace EscolaLms\Questionnaire\Services\Contracts;

use EscolaLms\Questionnaire\Models\Questionnaire;

/**
 * Interface QuestionnaireServiceContract
 * @package EscolaLms\Questionnaire\Http\Services\Contracts
 */
interface QuestionnaireServiceContract
{
    public function deleteQuestionnaire(Questionnaire $questionnaire): bool;
}
