<?php

namespace EscolaLms\Questionnaire\Enums;

use EscolaLms\Core\Enums\BasicEnum;

class QuestionnairePermissionsEnum extends BasicEnum
{
    const QUESTIONNAIRE_LIST = 'questionnaire_list';
    const QUESTIONNAIRE_READ = 'questionnaire_read';
    const QUESTIONNAIRE_CREATE = 'questionnaire_create';
    const QUESTIONNAIRE_DELETE = 'questionnaire_delete';
    const QUESTIONNAIRE_UPDATE = 'questionnaire_update';

    const QUESTION_LIST = 'question_list';
    const QUESTION_READ = 'question_read';
    const QUESTION_CREATE = 'question_create';
    const QUESTION_DELETE = 'question_delete';
    const QUESTION_UPDATE = 'question_update';
}
