<?php

namespace EscolaLms\Questionnaire\Services\Contracts;

use EscolaLms\Questionnaire\Models\Question;

/**
 * Interface QuestionServiceContract
 * @package EscolaLms\Questionnaire\Http\Services\Contracts
 */
interface QuestionServiceContract
{
    public function deleteQuestion(Question $question): bool;
}
