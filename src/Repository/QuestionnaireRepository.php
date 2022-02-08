<?php

namespace EscolaLms\Questionnaire\Repository;

use EscolaLms\Core\Repositories\BaseRepository;
use EscolaLms\Questionnaire\Models\Questionnaire;
use EscolaLms\Questionnaire\Repository\Contracts\QuestionnaireRepositoryContract;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class QuestionnaireRepository extends BaseRepository implements QuestionnaireRepositoryContract
{
    public function model(): string
    {
        return Questionnaire::class;
    }

    public function getFieldsSearchable(): array
    {
        return ['active', 'id', 'title'];
    }

    public function searchAndPaginate(
        array $search = [],
        ?int $perPage = null,
        string $orderDirection = 'asc',
        string $orderColumn = 'id'
    ): LengthAwarePaginator {
        $query = $this->allQuery($search);
        if (isset($search['model_type_id'])) {
            $query->select('questionnaires.*')
                ->join('questionnaire_models', 'questionnaire_models.questionnaire_id', '=', 'questionnaires.id')
                ->where('questionnaire_models.model_type_id', '=', $search['model_type_id'])
                ->where('questionnaire_models.model_id', '=', $search['model_id']);
        }

        return $query->orderBy($orderColumn, $orderDirection)->paginate($perPage);
    }

    public function insert(Questionnaire $questionnaire): Questionnaire
    {
        return $this->createUsingModel($questionnaire);
    }

    public function deleteQuestionnaire(int $id): bool
    {
        $questionnaire = $this->find($id);
        try {
            return $questionnaire->delete();
        } catch (\Exception $err) {
            return false;
        }
    }

    public function save(Questionnaire $questionnaire): bool
    {
        return $questionnaire->save();
    }

    public function findActive(int $id): Questionnaire
    {
        return $this->model->newQuery()->where('active', '=', true)->findOrFail($id);
    }
}
