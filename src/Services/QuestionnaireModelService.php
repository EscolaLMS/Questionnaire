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
        $questionnaireModels = $this->questionnaireModelRepository->all(['questionnaire_id' => $questionnaireId]);

        $existingModels = [];
        foreach ($models as $model) {
            $existingModels[] = QuestionnaireModel::updateOrCreate(
                [
                    'questionnaire_id' => $questionnaireId,
                    'model_type_id' => $model['model_type_id'],
                    'model_id' => $model['model_id'],
                    'target_group' => $model['target_group'] ?? null,
                ],
                [
                    'display_frequency_minutes' => $model['display_frequency_minutes'] ?? null,
                ]);
        }

        $questionnaireModels = $questionnaireModels->diffUsing(collect($existingModels), function ($a, $b) {
            return $a->id <=> $b->id;
        });

        foreach ($questionnaireModels as $model) {
            $this->deleteQuestionnaireModel($model);
        }
    }
}
