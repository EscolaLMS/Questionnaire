<?php

namespace EscolaLms\Questionnaire\Http\Controllers;

use EscolaLms\Core\Http\Controllers\EscolaLmsBaseController;
use EscolaLms\Questionnaire\Http\Controllers\Contracts\QuestionAdminApiContract;
use EscolaLms\Questionnaire\Http\Requests\QuestionCreateRequest;
use EscolaLms\Questionnaire\Http\Requests\QuestionDeleteRequest;
use EscolaLms\Questionnaire\Http\Requests\QuestionListingRequest;
use EscolaLms\Questionnaire\Http\Requests\QuestionReadRequest;
use EscolaLms\Questionnaire\Http\Requests\QuestionUpdateRequest;
use EscolaLms\Questionnaire\Http\Resources\QuestionResource;
use EscolaLms\Questionnaire\Models\Question;
use EscolaLms\Questionnaire\Repository\Contracts\QuestionRepositoryContract;
use EscolaLms\Questionnaire\Services\Contracts\QuestionServiceContract;
use Illuminate\Http\JsonResponse;

class QuestionAdminApiController extends EscolaLmsBaseController implements QuestionAdminApiContract
{
    private QuestionRepositoryContract $questionRepository;
    private QuestionServiceContract $questionService;

    public function __construct(
        QuestionRepositoryContract $questionRepository,
        QuestionServiceContract $questionService
    ) {
        $this->questionRepository = $questionRepository;
        $this->questionService = $questionService;
    }

    public function list(QuestionListingRequest $request): JsonResponse
    {
        $questions = $this->questionRepository->searchAndPaginate();

        return $this->sendResponseForResource(
            QuestionResource::collection($questions),
            __("Question list retrieved successfully")
        );
    }

    public function create(QuestionCreateRequest $request): JsonResponse
    {
        $question = new Question([
            'description' => $request->getParamDescription(),
            'title' => $request->getParamTitle(),
            'questionnaire_id' => $request->getParamQuestionnaireId(),
            'position' => $request->getParamPosition(),
            'active' => $request->getParamActive(),
        ]);

        $question = $this->questionRepository->insert($question);

        return $this->sendResponseForResource(
            QuestionResource::make($question),
            __("Question created successfully")
        );
    }

    public function update(QuestionUpdateRequest $request, int $id): JsonResponse
    {
        $input = $request->all();
        $updated = $this->questionRepository->update($input, $id);

        return $this->sendResponseForResource(
            QuestionResource::make($updated),
            __("Question updated successfully")
        );
    }

    public function delete(QuestionDeleteRequest $request, int $id): JsonResponse
    {
        $deleted = $this->questionService->deleteQuestion($request->getQuestion());
        if (!$deleted) {
            return $this->sendError(__("Can't delete question"), 404);
        }

        return $this->sendResponse(true, __("Question delete successfully"));
    }

    public function read(QuestionReadRequest $request, int $id): JsonResponse
    {
        $questions = $this->questionRepository->find($id);

        return $this->sendResponseForResource(
            QuestionResource::make($questions),
            __("Question fetched successfully")
        );
    }
}
