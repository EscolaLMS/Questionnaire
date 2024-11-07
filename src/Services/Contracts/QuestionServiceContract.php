<?php

namespace EscolaLms\Questionnaire\Services\Contracts;

use EscolaLms\Questionnaire\Models\Question;
use Illuminate\Support\Collection;

/**
 * Interface QuestionServiceContract
 * @package EscolaLms\Questionnaire\Http\Services\Contracts
 */
interface QuestionServiceContract
{
    public function deleteQuestion(Question $question): bool;

    public function createQuestion(array $data): Question;

    public function updateQuestion(Question $question, array $data): Question;
    public function getAllQuestionnaireQuestions(int $id): Collection;
}
