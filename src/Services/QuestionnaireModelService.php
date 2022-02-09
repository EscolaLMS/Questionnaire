<?php

namespace EscolaLms\Questionnaire\Services;

use EscolaLms\Questionnaire\Models\QuestionnaireModel;
use EscolaLms\Questionnaire\Repository\Contracts\QuestionAnswerRepositoryContract;
use EscolaLms\Questionnaire\Repository\Contracts\QuestionnaireModelRepositoryContract;
use EscolaLms\Questionnaire\Services\Contracts\QuestionnaireModelServiceContract;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class QuestionnaireModelService implements QuestionnaireModelServiceContract
{
    private QuestionAnswerRepositoryContract $questionAnswerRepository;
    private QuestionnaireModelRepositoryContract $questionnaireModelRepository;

    public function __construct(
        QuestionAnswerRepositoryContract $questionAnswerRepository,
        QuestionnaireModelRepositoryContract $questionnaireModelRepository
    ) {
        $this->questionAnswerRepository = $questionAnswerRepository;
        $this->questionnaireModelRepository = $questionnaireModelRepository;
    }

    public function deleteQuestionnaireModel(QuestionnaireModel $questionnaireModel): bool
    {
        DB::transaction(function () use ($questionnaireModel) {
            $this->questionAnswerRepository->deleteByModelId($questionnaireModel->id);
            $this->questionnaireModelRepository->delete($questionnaireModel->id);
        });

        return true;
    }

    public function saveModelsForQuestionnaire(int $questionnaireId, array $models): void
    {
        $questionnaireModel = $this->prepareArrayQuestionnaireModel(
            $this->questionnaireModelRepository->all(['questionnaire_id' => $questionnaireId])
        );
        foreach ($models as $model) {
            $key = $model['model_type_id'].'_'.$model['model_id'];
            if (!isset($questionnaireModel[$key])) {
                QuestionnaireModel::create([
                    'questionnaire_id' => $questionnaireId,
                    'model_type_id' => $model['model_type_id'],
                    'model_id' => $model['model_id'],
                ]);
            } else {
                unset($questionnaireModel[$key]);
            }
        }
        foreach ($questionnaireModel as $model) {
            $this->deleteQuestionnaireModel($model);
        }
    }

    private function prepareArrayQuestionnaireModel(Collection $questionnaireModel): array
    {
        $arrayQuestionnaireModel = [];
        foreach ($questionnaireModel as $model) {
            $arrayQuestionnaireModel[$model->model_type_id.'_'.$model->model_id] = $model;
        }

        return $arrayQuestionnaireModel;
    }
}
