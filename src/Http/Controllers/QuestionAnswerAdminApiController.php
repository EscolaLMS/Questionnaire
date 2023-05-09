<?php

namespace EscolaLms\Questionnaire\Http\Controllers;

use EscolaLms\Core\Dtos\OrderDto;
use EscolaLms\Core\Http\Controllers\EscolaLmsBaseController;
use EscolaLms\Questionnaire\Dtos\QuestionAnswerFilterCriteriaDto;
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

    public function list(int $id, QuestionAnswerListingRequest $request): JsonResponse
    {
        return $this->sendResponseForResource(
            QuestionAnswerResource::collection($this->questionAnswerRepository->listWithCriteriaAndOrder(
                $id,
                QuestionAnswerFilterCriteriaDto::instantiateFromRequest($request),
                OrderDto::instantiateFromRequest($request),
                $request->get('per_page', 20)
            )),
            __("Question answers list retrieved successfully")
        );
    }
}
