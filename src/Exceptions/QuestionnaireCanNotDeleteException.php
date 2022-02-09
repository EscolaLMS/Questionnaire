<?php

namespace EscolaLms\Questionnaire\Exceptions;

use Exception;

class QuestionnaireCanNotDeleteException extends Exception
{
    public function __construct(string $questionnaireId)
    {
        parent::__construct(__('Questionnaire :id can not be delete', ['id' => $questionnaireId]));
    }
}
