<?php

namespace EscolaLms\Questionnaire\Http\Controllers;

use EscolaLms\Core\Http\Controllers\EscolaLmsBaseController;
use EscolaLms\Questionnaire\Http\Controllers\Contracts\QuestionnaireApiContract;
use EscolaLms\Questionnaire\Http\Requests\QuestionnaireFrontListingRequest;
use EscolaLms\Questionnaire\Http\Requests\QuestionnaireFrontReadRequest;
use EscolaLms\Questionnaire\Http\Resources\QuestionnaireResource;
use EscolaLms\Questionnaire\Repository\Contracts\QuestionnaireRepositoryContract;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;

class QuestionnaireApiController extends EscolaLmsBaseController implements QuestionnaireApiContract
{
    private QuestionnaireRepositoryContract $questionnaireRepository;

    public function __construct(QuestionnaireRepositoryContract $questionnaireRepository)
    {
        $this->questionnaireRepository = $questionnaireRepository;
    }

    public function list(QuestionnaireFrontListingRequest $request): JsonResponse
    {
        $questionnaires = $this->questionnaireRepository->searchAndPaginate(['active' => true]);

        return $this->sendResponseForResource(
            QuestionnaireResource::collection($questionnaires),
            "questionnaire list retrieved successfully"
        );
    }

    public function read(QuestionnaireFrontReadRequest $request): JsonResponse
    {
        $id = $request->getParamId();
        $questionnaire = $this->questionnaireRepository->find($id);
        if ($questionnaire && $questionnaire->exists) {
            if (!$questionnaire->active) {
                return $this->sendError(
                    sprintf("You don't have access to questionnaire with id '%s'", $id),
                    403
                );
            }

            return $this->sendResponseForResource(
                QuestionnaireResource::make($questionnaire),
                "questionnaire fetched successfully"
            );
        }

        return $this->sendError(sprintf("Questionnaire with id '%s' doesn't exists", $id), 404);
    }
}
