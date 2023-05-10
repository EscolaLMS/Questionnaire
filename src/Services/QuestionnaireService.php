<?php

namespace EscolaLms\Questionnaire\Services;

use EscolaLms\Core\Dtos\OrderDto;
use EscolaLms\Core\Models\User;
use EscolaLms\Core\Repositories\Criteria\Primitives\WhereCriterion;
use EscolaLms\Questionnaire\Models\Question;
use EscolaLms\Questionnaire\Models\QuestionAnswer;
use EscolaLms\Questionnaire\Models\Questionnaire;
use EscolaLms\Questionnaire\Models\QuestionnaireModel;
use EscolaLms\Questionnaire\Repository\Contracts\QuestionnaireModelRepositoryContract;
use EscolaLms\Questionnaire\Repository\Contracts\QuestionnaireRepositoryContract;
use EscolaLms\Questionnaire\Services\Contracts\QuestionnaireModelServiceContract;
use EscolaLms\Questionnaire\Services\Contracts\QuestionnaireServiceContract;
use EscolaLms\Questionnaire\Services\Contracts\QuestionServiceContract;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class QuestionnaireService implements QuestionnaireServiceContract
{
    private QuestionnaireRepositoryContract $questionnaireRepository;
    private QuestionnaireModelServiceContract $questionnaireModelService;
    private QuestionnaireModelRepositoryContract $questionnaireModelRepository;
    private QuestionServiceContract $questionService;

    public function __construct(
        QuestionnaireRepositoryContract      $questionnaireRepository,
        QuestionnaireModelServiceContract    $questionnaireModelService,
        QuestionServiceContract              $questionService,
        QuestionnaireModelRepositoryContract $questionnaireModelRepository
    )
    {
        $this->questionnaireRepository = $questionnaireRepository;
        $this->questionnaireModelService = $questionnaireModelService;
        $this->questionService = $questionService;
        $this->questionnaireModelRepository = $questionnaireModelRepository;
    }

    public function deleteQuestionnaire(Questionnaire $questionnaire): bool
    {
        DB::transaction(function () use ($questionnaire) {
            foreach ($questionnaire->questions as $question) {
                $this->questionService->deleteQuestion($question);
            }
            foreach ($questionnaire->questionnaireModels as $questionnaireModels) {
                $this->questionnaireModelService->deleteQuestionnaireModel($questionnaireModels);
            }
            $this->questionnaireRepository->delete($questionnaire->id);
        });

        return true;
    }

    public function searchForFront(array $filters, ?bool $publicAnswers = null): LengthAwarePaginator
    {
        $with = [];
        if (!is_null($publicAnswers)) {
            $with = [
                'questions' => fn (HasMany $q) => $q->where('public_answers', '=', $publicAnswers),
            ];
        }
        return $this
            ->questionnaireRepository
            ->searchByCriteriaAndPaginate(
                array_merge($filters, [new WhereCriterion('active', '=', true)]),
                $with
            );
    }

    public function list(array $criteria, OrderDto $orderDto): LengthAwarePaginator
    {
        $query = $this->questionnaireRepository->queryWithAppliedCriteria($criteria);
        if ($orderDto->getOrderBy()) {
            $query->orderBy($orderDto->getOrderBy(), $orderDto->getOrder() ?? 'asc');
        }

        return $query->paginate();
    }

    public function findForFront(array $filters, User $user): ?array
    {
        $questionnaire = $this->questionnaireRepository->findActive($filters['questionnaire_id']);

        $questionnaireModel = $this->questionnaireModelRepository->findByModelTitleAndModelId(
            $filters['model_type_title'],
            $filters['model_id'],
            $filters['questionnaire_id']
        );

        if (!$questionnaire) {
            return null;
        }

        $questionnaireReturn = $questionnaire->toArray();
        foreach ($questionnaire->questions as $key => $question) {
            $questionnaireReturn['questions'][$key] = $question->toArray();
            $answer = $this->getAnswerFromQuestionForUser($question, $questionnaireModel, $user);
            $questionnaireReturn['questions'][$key]['rate'] = $answer ? $answer->rate : null;
            $questionnaireReturn['questions'][$key]['type'] = $question->type;
            $questionnaireReturn['questions'][$key]['note'] = $answer ? $answer->note : null;
        }
        $questionnaireReturn['questions'] = new collection($questionnaireReturn['questions'] ?? []);

        return $questionnaireReturn;
    }

    public function answer(array $params, User $user): bool
    {
        return true;
    }

    private function getAnswerFromQuestionForUser(
        Question            $question,
        ?QuestionnaireModel $model,
        User                $user
    ): ?QuestionAnswer
    {
        return $model ? $question->answers()->where([
            'user_id' => $user->id,
            'questionnaire_model_id' => $model->id
        ])->first() : null;
    }

    public function createQuestionnaire(array $data): Questionnaire
    {
        $questionnaire = new Questionnaire([
            'title' => $data['title'],
            'active' => $data['active']
        ]);
        $questionnaire->save();

        foreach ($data['models'] ?? [] as $model) {
            QuestionnaireModel::create([
                'questionnaire_id' => $questionnaire->getKey(),
                'model_type_id' => $model['model_type_id'],
                'model_id' => $model['model_id'],
            ]);
        }

        return $questionnaire->refresh();
    }

    public function updateQuestionnaire(Questionnaire $questionnaire, array $data): Questionnaire
    {
        if (isset($data['models'])) {
            $this->questionnaireModelService->saveModelsForQuestionnaire($questionnaire->getKey(), $data['models']);
        }

        unset($data['models']);

        $questionnaire->fill($data)->save();

        return $questionnaire->refresh();
    }
}
