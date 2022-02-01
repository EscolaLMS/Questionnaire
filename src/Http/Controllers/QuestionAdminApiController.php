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
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class QuestionAdminApiController extends EscolaLmsBaseController implements QuestionAdminApiContract
{
    private QuestionRepositoryContract $questionRepository;

    public function __construct(QuestionRepositoryContract $questionRepository)
    {
        $this->questionRepository = $questionRepository;
    }

    public function list(QuestionListingRequest $request): JsonResponse
    {
        $questions = $this->questionRepository->searchAndPaginate();

        return $this->sendResponseForResource(
            QuestionResource::collection($questions),
            "Question list retrieved successfully"
        );
    }

    public function create(QuestionCreateRequest $request): JsonResponse
    {
        /** @var Question $question */
        $question = Question::factory()->newModel([
            'description' => $request->getParamDescription(),
            'title' => $request->getParamTitle(),
            'questionnaire_id' => $request->getParamQuestionnaireId(),
            'position' => $request->getParamPosition(),
            'active' => $request->get('active'),
        ]);

        $question = $this->questionRepository->insert($question);

        return $this->sendResponseForResource(
            QuestionResource::make($question),
            "Question created successfully"
        );
    }

    public function update(QuestionUpdateRequest $request, int $id): JsonResponse
    {
        $input = $request->all();

        $updated = $this->questionRepository->update($input, $id);
        if (!$updated) {
            return $this->sendError(sprintf("Question with slug '%s' doesn't exists", $id), 404);
        }

        return $this->sendResponseForResource(
            QuestionResource::make($updated),
            "Question updated successfully"
        );
    }

    public function delete(QuestionDeleteRequest $request, int $id): JsonResponse
    {
        $deleted = $this->questionRepository->delete($id);
        if (!$deleted) {
            return $this->sendError(sprintf("Question with id '%s' doesn't exists", $id), 404);
        }

        return $this->sendResponse($deleted, "Question delete successfully");
    }

    public function read(QuestionReadRequest $request, int $id): JsonResponse
    {
        $questions = $this->questionRepository->find($id);
        if ($questions && $questions->exists) {
            return $this->sendResponseForResource(
                QuestionResource::make($questions),
                "Question fetched successfully"
            );
        }

        return $this->sendError(sprintf("Question with id '%s' doesn't exists", $id), 404);
    }
}
