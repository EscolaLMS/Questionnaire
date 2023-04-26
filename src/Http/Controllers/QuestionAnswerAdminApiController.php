<?php

namespace EscolaLms\Questionnaire\Http\Controllers;

use EscolaLms\Core\Http\Controllers\EscolaLmsBaseController;
use EscolaLms\Questionnaire\Http\Controllers\Contracts\QuestionAnswerAdminApiContract;
use EscolaLms\Questionnaire\Http\Requests\QuestionAnswerListingRequest;
use EscolaLms\Questionnaire\Http\Requests\QuestionAnswerVisibilityRequest;
use EscolaLms\Questionnaire\Http\Resources\QuestionAnswerResource;
use EscolaLms\Questionnaire\Services\Contracts\QuestionnaireAnswerServiceContract;
use Illuminate\Http\JsonResponse;

class QuestionAnswerAdminApiController extends EscolaLmsBaseController implements QuestionAnswerAdminApiContract
{
    private QuestionnaireAnswerServiceContract $questionnaireAnswerService;

    public function __construct(QuestionnaireAnswerServiceContract $questionnaireAnswerService)
    {
        $this->questionnaireAnswerService = $questionnaireAnswerService;
    }

    public function list(QuestionAnswerListingRequest $request): JsonResponse
    {
        return $this->sendResponseForResource(
            QuestionAnswerResource::collection($this->questionnaireAnswerService->searchAndPaginate($request->validated())),
            __("Question answers list retrieved successfully")
        );
    }

    public function changeAnswerVisibility(QuestionAnswerVisibilityRequest $request, int $id): JsonResponse
    {
        return $this->sendResponseForResource(
            QuestionAnswerResource::make($this->questionnaireAnswerService->update($request->validated(), $id)),
            __('Question answers visibility changed successfully')
        );
    }
}
