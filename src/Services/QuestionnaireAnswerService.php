<?php

namespace EscolaLms\Questionnaire\Services;

use EscolaLms\Questionnaire\Repository\Contracts\QuestionAnswerRepositoryContract;
use EscolaLms\Questionnaire\Services\Contracts\QuestionnaireAnswerServiceContract;
use Illuminate\Support\Collection;

class QuestionnaireAnswerService implements QuestionnaireAnswerServiceContract
{
    private QuestionAnswerRepositoryContract $questionAnswerRepository;

    public function __construct(QuestionAnswerRepositoryContract $questionAnswerRepository)
    {
        $this->questionAnswerRepository = $questionAnswerRepository;
    }

    public function getReport(int $id, ?int $model_type_id = null, ?int $model_id = null, ?int $user_id = null): Collection
    {
        $report = $this->questionAnswerRepository->getReport($id, $model_type_id, $model_id, $user_id)->toArray();

        return new collection($report);
    }
}
