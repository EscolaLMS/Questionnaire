<?php

namespace EscolaLms\Questionnaire\Repository;

use EscolaLms\Core\Repositories\BaseRepository;
use EscolaLms\Questionnaire\Models\Questionnaire;
use EscolaLms\Questionnaire\Repository\Contracts\QuestionnaireRepositoryContract;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class QuestionnaireRepository extends BaseRepository implements QuestionnaireRepositoryContract
{
    public function model()
    {
        return Questionnaire::class;
    }

    public function getFieldsSearchable()
    {
        return [];
    }

    public function searchAndPaginate(array $search = [], ?int $perPage = null, string $orderDirection = 'asc', string $orderColumn = 'id'): LengthAwarePaginator
    {
        return $this->allQuery($search)->orderBy($orderColumn, $orderDirection)->paginate($perPage);
    }

    /**
     * @param Questionnaire $page
     * @return Questionnaire
     */
    public function insert(Questionnaire $questionnaire): Questionnaire
    {
        return $this->createUsingModel($questionnaire);
    }

    /**
     * @param int $id
     * @return bool
     */
    public function deleteQuestionnaire(int $id): bool
    {
        $questionnaire = $this->find($id);
        if (!$questionnaire) {
            return false;
        }
        //TODO remove all questions
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
}
