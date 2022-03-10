<?php

namespace EscolaLms\Questionnaire\Http\Controllers;

use EscolaLms\Core\Http\Controllers\EscolaLmsBaseController;
use EscolaLms\Questionnaire\Http\Controllers\Contracts\QuestionAnswerAdminApiContract;
use EscolaLms\Questionnaire\Http\Requests\QuestionAnswerListingRequest;
use EscolaLms\Questionnaire\Http\Resources\QuestionAnswerResource;
use EscolaLms\Questionnaire\Repository\Contracts\QuestionAnswerRepositoryContract;
use Illuminate\Http\JsonResponse;

class QuestionAnswerAdminApiController extends EscolaLmsBaseController implements QuestionAnswerAdminApiContract
{
    private QuestionAnswerRepositoryContract $questionAnswerRepository;

    public function __construct(QuestionAnswerRepositoryContract $questionAnswerRepository)
    {
        $this->questionAnswerRepository = $questionAnswerRepository;
    }

    public function list(QuestionAnswerListingRequest $request): JsonResponse
    {
        return $this->sendResponseForResource(
            QuestionAnswerResource::collection($this->questionAnswerRepository->searchAndPaginate($request->validated())),
            __("Question answers list retrieved successfully")
        );
    }
}
