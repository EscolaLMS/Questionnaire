<?php

namespace EscolaLms\Questionnaire\Services;

use EscolaLms\Core\Models\User;
use EscolaLms\Questionnaire\Models\Question;
use EscolaLms\Questionnaire\Models\QuestionAnswer;
use EscolaLms\Questionnaire\Models\Questionnaire;
use EscolaLms\Questionnaire\Models\QuestionnaireModel;
use EscolaLms\Questionnaire\Repository\Contracts\QuestionnaireModelRepositoryContract;
use EscolaLms\Questionnaire\Repository\Contracts\QuestionnaireRepositoryContract;
use EscolaLms\Questionnaire\Repository\Contracts\QuestionRepositoryContract;
use EscolaLms\Questionnaire\Services\Contracts\QuestionnaireModelServiceContract;
use EscolaLms\Questionnaire\Services\Contracts\QuestionnaireServiceContract;
use EscolaLms\Questionnaire\Services\Contracts\QuestionServiceContract;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class QuestionnaireService implements QuestionnaireServiceContract
{
    private QuestionnaireRepositoryContract $questionnaireRepository;
    private QuestionnaireModelServiceContract $questionnaireModelService;
    private QuestionnaireModelRepositoryContract $questionnaireModelRepository;
    private QuestionServiceContract $questionService;
    private QuestionRepositoryContract $questionRepository;

    public function __construct(
        QuestionnaireRepositoryContract $questionnaireRepository,
        QuestionnaireModelServiceContract $questionnaireModelService,
        QuestionServiceContract $questionService,
        QuestionRepositoryContract $questionRepository,
        QuestionnaireModelRepositoryContract $questionnaireModelRepository
    ) {
        $this->questionnaireRepository = $questionnaireRepository;
        $this->questionnaireModelService = $questionnaireModelService;
        $this->questionService = $questionService;
        $this->questionRepository = $questionRepository;
        $this->questionnaireModelRepository = $questionnaireModelRepository;
    }

    public function deleteQuestionnaire(Questionnaire $questionnaire): bool
    {
        try {
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
        } catch (\Exception $err) {
            return false;
        }
    }

    public function searchForFront(array $filters, User $user): LengthAwarePaginator
    {


        return $this->questionnaireRepository->searchAndPaginate($filters);
    }

    public function findForFront(array $filters, User $user): ?array
    {
        $questionnaire = $this->questionnaireRepository->findActive($filters['id']);

        $questionnaireModel = $this->questionnaireModelRepository->findByModelTitleAndModelId(
            $filters['model_type_title'],
            $filters['model_id']
        );

        if (!$questionnaire) {
            return null;
        }

        $questionnaireReturn = $questionnaire->toArray();
        foreach ($questionnaire->questions as $key => $question) {
            $questionnaireReturn['questions'][$key] = $question->toArray();
            $answer = $this->getAnswerFromQuestionForUser($question, $questionnaireModel, $user);
            $questionnaireReturn['questions'][$key]['rate'] = $answer ? $answer->rate : null;
        }
        $questionnaireReturn['questions'] = new collection($questionnaireReturn['questions'] ?? []);

        return $questionnaireReturn;
    }

    public function answer(array $params, User $user): bool
    {
        return true;
    }

    private function getAnswerFromQuestionForUser(
        Question $question,
        ?QuestionnaireModel $model,
        User $user
    ): ?QuestionAnswer {
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

        $questionnaire->fill($data);
        $questionnaire->save();

        return $questionnaire->refresh();
    }
}
