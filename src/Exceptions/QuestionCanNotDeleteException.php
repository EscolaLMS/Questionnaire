<?php

namespace EscolaLms\Questionnaire\Exceptions;

use Exception;

class QuestionCanNotDeleteException extends Exception
{
    public function __construct(string $questionId)
    {
        parent::__construct(__('Question :id can not be delete', ['id' => $questionId]));
    }
}
