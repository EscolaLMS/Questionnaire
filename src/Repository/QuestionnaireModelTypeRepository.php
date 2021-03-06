<?php

namespace EscolaLms\Questionnaire\Repository;

use EscolaLms\Core\Repositories\BaseRepository;
use EscolaLms\Questionnaire\Models\QuestionnaireModelType;
use EscolaLms\Questionnaire\Repository\Contracts\QuestionnaireModelTypeRepositoryContract;

class QuestionnaireModelTypeRepository extends BaseRepository implements QuestionnaireModelTypeRepositoryContract
{
    public function model(): string
    {
        return QuestionnaireModelType::class;
    }

    public function getFieldsSearchable(): array
    {
        return [
            'id',
            'title',
            'model_class',
        ];
    }
}
