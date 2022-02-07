<?php

namespace EscolaLms\Questionnaire\Services;

use EscolaLms\Core\Models\User;
use EscolaLms\Questionnaire\Models\QuestionAnswer;
use EscolaLms\Questionnaire\Models\QuestionnaireModel;
use EscolaLms\Questionnaire\Repository\Contracts\QuestionAnswerRepositoryContract;
use EscolaLms\Questionnaire\Services\Contracts\QuestionnaireAnswerServiceContract;
use EscolaLms\Questionnaire\Services\Contracts\QuestionnaireServiceContract;
use Illuminate\Support\Collection;

class QuestionnaireAnswerService implements QuestionnaireAnswerServiceContract
{
    private QuestionAnswerRepositoryContract $questionAnswerRepository;
    private QuestionnaireServiceContract $questionnaireService;

    public function __construct(
        QuestionAnswerRepositoryContract $questionAnswerRepository,
        QuestionnaireServiceContract $questionnaireService
    ) {
        $this->questionAnswerRepository = $questionAnswerRepository;
        $this->questionnaireService = $questionnaireService;
    }

    public function getReport(int $id, ?int $model_type_id = null, ?int $model_id = null, ?int $user_id = null): Collection
    {
        $report = $this->questionAnswerRepository->getReport($id, $model_type_id, $model_id, $user_id)->toArray();

        return new collection($report);
    }

    public function saveAnswers(QuestionnaireModel $questionnaireModel, array $data, User $user): ?array
    {
        $oldAnswers = $this->questionAnswerRepository->makeModel()->newQuery()->where([
                'questionnaire_model_id' => $questionnaireModel->getKey(),
                'user_id' => $user->getKey()
            ])->get()->keyBy('question_id');
        foreach ($data['answers'] ?? [] as $answer) {
            if (!isset($oldAnswers[$answer['question_id']])) {
                QuestionAnswer::create([
                    'user_id' => $user->getKey(),
                    'question_id' => $answer['question_id'],
                    'questionnaire_model_id' => $questionnaireModel->getKey(),
                    'rate' => $answer['rate'],
                ]);
            } else {
                $oldAnswers[$answer['question_id']]->fill(['rate' => $answer['rate']])->save();
            }
        }

        return $this->questionnaireService->findForFront(
            [
                'id' => $questionnaireModel->questionnaire_id,
                'model_type_title' => $questionnaireModel->modelableType->title,
                'model_id' => $questionnaireModel->model_id,
            ],
            $user
        );
    }
}
