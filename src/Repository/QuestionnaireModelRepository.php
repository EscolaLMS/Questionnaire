<?php

namespace EscolaLms\Questionnaire\Repository;

use EscolaLms\Core\Repositories\BaseRepository;
use EscolaLms\Questionnaire\Models\QuestionnaireModel;
use EscolaLms\Questionnaire\Repository\Contracts\QuestionnaireModelRepositoryContract;

class QuestionnaireModelRepository extends BaseRepository implements QuestionnaireModelRepositoryContract
{
    public function model(): string
    {
        return QuestionnaireModel::class;
    }

    public function getFieldsSearchable(): array
    {
        return [];
    }
}
