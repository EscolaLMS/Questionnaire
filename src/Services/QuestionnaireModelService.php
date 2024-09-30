<?php

namespace EscolaLms\Questionnaire\Services;

use EscolaLms\Questionnaire\Dtos\QuestionnaireModelDto;
use EscolaLms\Questionnaire\Models\QuestionnaireModel;
use EscolaLms\Questionnaire\Models\QuestionnaireModelType;
use EscolaLms\Questionnaire\Repository\Contracts\QuestionAnswerRepositoryContract;
use EscolaLms\Questionnaire\Repository\Contracts\QuestionnaireModelRepositoryContract;
use EscolaLms\Questionnaire\Services\Contracts\QuestionnaireModelServiceContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class QuestionnaireModelService implements QuestionnaireModelServiceContract
{
    private QuestionAnswerRepositoryContract $questionAnswerRepository;
    private QuestionnaireModelRepositoryContract $questionnaireModelRepository;

    public function __construct(
        QuestionAnswerRepositoryContract     $questionAnswerRepository,
        QuestionnaireModelRepositoryContract $questionnaireModelRepository
    )
    {
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
            $existingModels[] = $this->questionnaireModelRepository->updateOrCreate(
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

    public function assign(QuestionnaireModelType $questionnaireModelType, QuestionnaireModelDto $dto): QuestionnaireModel
    {
        return $this->questionnaireModelRepository->updateOrCreate(
            [
                'questionnaire_id' => $dto->getId(),
                'model_type_id' => $questionnaireModelType->getKey(),
                'model_id' => $dto->getModelId(),
                'target_group' => $dto->getTargetGroup(),
            ],
            [
                'display_frequency_minutes' => $dto->getDisplayFrequencyMinutes(),
            ]
        );
    }

    public function unassign(QuestionnaireModelType $questionnaireModelType, QuestionnaireModelDto $dto): void
    {
         $this->questionnaireModelRepository->allQuery([
            'questionnaire_id' => $dto->getId(),
            'model_type_id' => $questionnaireModelType->getKey(),
            'model_id' => $dto->getModelId(),
        ])
         ->when($dto->getTargetGroup(), fn(Builder $query) => $query->where('target_group', $dto->getTargetGroup()))
         ->delete();
    }
}
