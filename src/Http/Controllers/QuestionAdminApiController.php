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
        return $this->sendResponseForResource(
            QuestionResource::collection($this->questionRepository->searchAndPaginate()),
            __("Question list retrieved successfully")
        );
    }

    public function create(QuestionCreateRequest $request): JsonResponse
    {
        $questionnaire = $this->questionService->createQuestion($request->validated());

        return $this->sendResponseForResource(
            QuestionResource::make($questionnaire),
            __("Question created successfully")
        );
    }

    public function update(QuestionUpdateRequest $request, int $id): JsonResponse
    {
        $updated = $this->questionService->updateQuestion($request->getQuestion(), $request->validated());

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

    public function read(QuestionReadRequest $request): JsonResponse
    {
        return $this->sendResponseForResource(
            QuestionResource::make($request->getQuestion()),
            __("Question fetched successfully")
        );
    }
}
