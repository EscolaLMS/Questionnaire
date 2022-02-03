<?php

namespace EscolaLms\Questionnaire\Services;

use EscolaLms\Questionnaire\Models\Question;
use EscolaLms\Questionnaire\Repository\Contracts\QuestionAnswerRepositoryContract;
use EscolaLms\Questionnaire\Repository\Contracts\QuestionRepositoryContract;
use EscolaLms\Questionnaire\Services\Contracts\QuestionServiceContract;
use Illuminate\Support\Facades\DB;

class QuestionService implements QuestionServiceContract
{
    private QuestionAnswerRepositoryContract $questionAnswerRepository;
    private QuestionRepositoryContract $questionRepository;

    public function __construct(
        QuestionAnswerRepositoryContract $questionAnswerRepository,
        QuestionRepositoryContract $questionRepository
    ) {
        $this->questionAnswerRepository = $questionAnswerRepository;
        $this->questionRepository = $questionRepository;
    }

    public function deleteQuestion(Question $question): bool
    {
        try {
            DB::transaction(function () use ($question) {
                $this->questionAnswerRepository->deleteByQuestionId($question->id);
                $this->questionRepository->delete($question->id);
            });

            return true;
        } catch (\Exception $err) {
            return false;
        }
    }
}
