<?php

namespace EscolaLms\Questionnaire\Services;

use EscolaLms\Consultations\Models\Consultation;
use EscolaLms\Questionnaire\Dtos\QuestionnaireModelDto;
use EscolaLms\Questionnaire\Models\QuestionAnswer;
use EscolaLms\Questionnaire\Models\QuestionnaireModel;
use EscolaLms\Questionnaire\Models\QuestionnaireModelType;
use EscolaLms\Questionnaire\Repository\Contracts\QuestionAnswerRepositoryContract;
use EscolaLms\Questionnaire\Repository\Contracts\QuestionnaireModelRepositoryContract;
use EscolaLms\Questionnaire\Services\Contracts\QuestionnaireModelServiceContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
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

    public function getQuestionnaireDataToExport(QuestionnaireModelDto $dto, QuestionnaireModelType $questionnaireModelType): Collection
    {
        $result = QuestionAnswer::query()
            ->select([
                'question_answers.user_id',
                'users.email',
                'users.first_name',
                'users.last_name',
                'questionnaires.id as questionnaire_id',
                'questionnaires.title as questionnaire_title',
                'question_answers.question_id',
                'questions.title as question_title',
                'questions.type as question_type',
                DB::raw("STRING_AGG(COALESCE(question_answers.rate::text, ''), ';' ORDER BY question_answers.created_at) as rates"), // najwyższy rate dla użytkownika w danym kwestionariuszu
                DB::raw("STRING_AGG(COALESCE(question_answers.note, ''), ';' ORDER BY question_answers.created_at) as notes"), // łączy notatki z separatorami
                DB::raw("STRING_AGG(COALESCE(question_answers.created_at::text, ''), ';' ORDER BY question_answers.created_at) as answer_dates") // najnowsza odpowiedź
            ])
            ->leftJoin('questionnaire_models', 'questionnaire_models.id', '=', 'question_answers.questionnaire_model_id')
            ->leftJoin('questionnaires', 'questionnaires.id', '=', 'questionnaire_models.questionnaire_id')
            ->leftJoin('questions', 'questions.id', '=', 'question_answers.question_id')
            ->leftJoin('users', 'users.id', '=', 'question_answers.user_id')
            ->whereHas('questionnaireModel', fn ($query) => $query
                ->where('model_type_id', '=', $questionnaireModelType->getKey())
                ->where('model_id', '=', $dto->getModelId())
                ->whereHas('questionnaire', fn ($query) => $query->where('id', '=', $dto->getId()))
            );

        $groupBy = [
            'question_answers.user_id',
            'questionnaires.id',
            'questionnaires.title',
            'question_answers.question_id',
            'questions.title',
            'questions.type',
            'users.email',
            'users.first_name',
            'users.last_name',
        ];

        return match ($questionnaireModelType->model_class) {
            Consultation::class => $this->forConsultations($result, $groupBy, $dto),
            default => $this->default($result, $groupBy, $dto),
        };
    }

    private function default(Builder $result, array $groupBy, QuestionnaireModelDto $dto): Collection
    {
        $result = $result
            ->groupBy(...$groupBy)
            ->get();

        foreach ($result as $value) {
            $rates = explode(';', $value->rates);
            $notes = explode(';', $value->notes);
            $dates = explode(';', $value->answer_dates);

            for ($i = 0; $i < count($dates); $i++) {
                $value->{$value->question_title . ' - rate #' . $i} = $rates[$i];
                $value->{$value->question_title . ' - note #' . $i} = $notes[$i];
                $value->{$value->question_title . ' - date #' . $i} = $dates[$i];
                $answerTime = Carbon::make($dates[$i]);
                $value->{$value->question_title . ' - timestamp #' . $i} = $answerTime->timestamp;
            }
        }

        return $result;
    }

    private function forConsultations(Builder $result, array $groupBy, QuestionnaireModelDto $dto): Collection
    {
        $result
            ->addSelect([
                'consultation_user.id as consultation_user_id',
                'consultation_user_terms.executed_at as consultation_start',
                'consultation_user_terms.finished_at as consultation_end',
            ])
            ->leftJoin('consultation_user', function (JoinClause $join) use ($dto) {
                $join->on('question_answers.user_id', '=', 'consultation_user.user_id')
                    ->where('consultation_user.consultation_id', '=', $dto->getModelId());
            })
            ->leftJoin('consultation_user_terms', 'consultation_user_terms.consultation_user_id', '=', 'consultation_user.id')->orderBy('consultation_user_id')->orderBy('consultation_start');

        $groupBy = array_merge($groupBy, ['consultation_user.id', 'consultation_user_terms.executed_at', 'consultation_user_terms.finished_at',]);

        $result = $result
            ->groupBy(...$groupBy)
            ->get();

        $lastAnswerIndex = 0;
        for ($i = 0; $i < $result->count(); $i++) {
            $value = $result->get($i);
            $previous = $i > 0 ? $result->get($i - 1) : null;
            $next = $i === $result->count() - 1 ? null : $result->get($i + 1);

            $datesGraterThan = $value->consultation_start;
            if (!$previous || $previous->user_id !== $value->user_id) {
                $lastAnswerIndex = 0;
            }

            $datesLessThan = $next && $next->user_id === $value->user_id ? $next->consultation_start : null;

            $rates = explode(';', $value->rates);
            $notes = explode(';', $value->notes);
            $dates = explode(';', $value->answer_dates);

            while ($lastAnswerIndex < count($dates) && ($datesLessThan === null || Carbon::make($datesLessThan)->isAfter($dates[$lastAnswerIndex]))) {
                $consultationStartTimestamp = Carbon::make($value->consultation_start)->timestamp;
                $value->{$consultationStartTimestamp . ' - rate #' . $lastAnswerIndex . ' - ' . $value->question_title} = $rates[$lastAnswerIndex];
                $value->{$consultationStartTimestamp . ' - note #' . $lastAnswerIndex . ' - ' . $value->question_title} = $notes[$lastAnswerIndex];
                $value->{$consultationStartTimestamp . ' - date #' . $lastAnswerIndex . ' - ' . $value->question_title} = $dates[$lastAnswerIndex];
                $answerTime = Carbon::make($dates[$lastAnswerIndex]);
                $value->{$consultationStartTimestamp . ' - timestamp #' . $lastAnswerIndex . ' - ' . $value->question_title} = $answerTime->timestamp;

                $value->{$consultationStartTimestamp . ' - seconds after start #' . $lastAnswerIndex . ' - ' . $value->question_title} = $answerTime->diffInSeconds(Carbon::make($value->consultation_start));
                $value->{$consultationStartTimestamp . ' - answered after consultation #' . $lastAnswerIndex . ' - ' . $value->question_title} = $answerTime->isAfter(Carbon::make($value->consultation_end)) ? 'Tak' : 'Nie' ;
                $lastAnswerIndex++;
            }
        }

        return $result;
    }
}
