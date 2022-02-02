<?php

namespace EscolaLms\Questionnaire\Repository;

use EscolaLms\Core\Repositories\BaseRepository;
use EscolaLms\Questionnaire\Models\QuestionnaireModelType;
use EscolaLms\Questionnaire\Repository\Contracts\QuestionnaireModelTypeRepositoryContract;

class QuestionnaireModelTypeRepository extends BaseRepository implements QuestionnaireModelTypeRepositoryContract
{
    public function model()
    {
        return QuestionnaireModelType::class;
    }

    public function getFieldsSearchable()
    {
        return [];
    }
}
