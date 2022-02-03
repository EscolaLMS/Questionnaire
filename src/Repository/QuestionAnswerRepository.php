<?php

namespace EscolaLms\Questionnaire\Repository;

use EscolaLms\Core\Repositories\BaseRepository;
use EscolaLms\Questionnaire\Models\Question;
use EscolaLms\Questionnaire\Models\QuestionAnswer;
use EscolaLms\Questionnaire\Repository\Contracts\QuestionAnswerRepositoryContract;
use Illuminate\Database\Eloquent\Collection;

class QuestionAnswerRepository extends BaseRepository implements QuestionAnswerRepositoryContract
{
    public function model(): string
    {
        return QuestionAnswer::class;
    }

    public function getFieldsSearchable(): array
    {
        return [];
    }

    public function insert(Question $question): Question
    {
        return $this->createUsingModel($question);
    }

    public function save(Question $question): bool
    {
        return $question->save();
    }

    public function getReport(
        int $questionnaireId,
        ?int $modelTypeId = null,
        ?int $modelId = null,
        ?int $userId = null
    ): Collection {
        $query = $this
            ->model
            ->newQuery()
            ->selectRaw('SUM(rate) as sum_rate, COUNT(rate) as count_answers, AVG(rate) as avg_rate, question_id, questions.title')
            ->join('questions', 'question_id', '=', 'questions.id')
            ->where('questions.questionnaire_id', '=', $questionnaireId)
            ->groupBy('question_id', 'questions.title');
        if ($modelTypeId) {
            $query->join('questionnaire_models', 'question_id', '=', 'questionnaire_model_id')
                ->where('questionnaire_models.modelable_type_id', '=', $modelTypeId);
            if ($modelId) {
                $query->where('questionnaire_models.modelable_id', '=', $modelId);
            }
        }
        if ($userId) {
            $query->where('user_id', '=', $userId);
        }

        return $query->get();
    }

    public function deleteByModelId(int $modelId): bool
    {
        return $this->model->newQuery()->where('questionnaire_model_id', '=', $modelId)->delete();
    }

    public function deleteByQuestionId(int $questionId): bool
    {
        return $this->model->newQuery()->where('question_id', '=', $questionId)->delete();
    }
}