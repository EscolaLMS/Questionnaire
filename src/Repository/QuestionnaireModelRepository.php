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
        return [
            'id',
            'questionnaire_id',
            'model_type_id',
            'model_id',
        ];
    }

    public function findByModelTitleAndModelId(string $title, int $model_id): QuestionnaireModel
    {
        return $this
            ->model
            ->newQuery()
            ->select('questionnaire_models.*')
            ->join('questionnaire_model_types', 'questionnaire_model_types.id', '=', 'model_type_id')
            ->where('questionnaire_model_types.title', '=', $title)
            ->where('model_id', '=', $model_id)
            ->firstOrFail();
    }
}
