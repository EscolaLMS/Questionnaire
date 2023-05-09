<?php

namespace EscolaLms\Questionnaire\Http\Controllers;

use EscolaLms\Core\Http\Controllers\EscolaLmsBaseController;
use EscolaLms\Questionnaire\Dtos\QuestionAnswersCriteriaDto;
use EscolaLms\Questionnaire\Dtos\QuestionnaireFrontFilterCriteriaDto;
use EscolaLms\Questionnaire\Http\Controllers\Contracts\QuestionnaireApiContract;
use EscolaLms\Questionnaire\Http\Requests\QuestionAnswersFrontReadRequest;
use EscolaLms\Questionnaire\Http\Requests\QuestionAnswersFrontStarsRequest;
use EscolaLms\Questionnaire\Http\Requests\QuestionnaireFrontAnswerRequest;
use EscolaLms\Questionnaire\Http\Requests\QuestionnaireFrontListingRequest;
use EscolaLms\Questionnaire\Http\Requests\QuestionnaireFrontReadRequest;
use EscolaLms\Questionnaire\Http\Requests\QuestionnaireStarsFrontRequest;
use EscolaLms\Questionnaire\Http\Resources\ModelStarsResponse;
use EscolaLms\Questionnaire\Http\Resources\QuestionAnswerFrontResource;
use EscolaLms\Questionnaire\Http\Resources\QuestionnaireFrontResource;
use EscolaLms\Questionnaire\Http\Resources\QuestionnaireResource;
use EscolaLms\Questionnaire\Http\Resources\QuestionnaireStarsResource;
use EscolaLms\Questionnaire\Models\QuestionnaireModel;
use EscolaLms\Questionnaire\Services\Contracts\QuestionnaireAnswerServiceContract;
use EscolaLms\Questionnaire\Services\Contracts\QuestionnaireServiceContract;
use Illuminate\Http\JsonResponse;

class QuestionnaireApiController extends EscolaLmsBaseController implements QuestionnaireApiContract
{
    private QuestionnaireServiceContract $questionnaireService;
    private QuestionnaireAnswerServiceContract $questionnaireAnswerService;

    public function __construct(
        QuestionnaireServiceContract $questionnaireService,
        QuestionnaireAnswerServiceContract $questionnaireAnswerService
    ) {
        $this->questionnaireService = $questionnaireService;
        $this->questionnaireAnswerService = $questionnaireAnswerService;
    }

    public function list(QuestionnaireFrontListingRequest $request): JsonResponse
    {
        $questionnaires = $this->questionnaireService->searchForFront(
            QuestionnaireFrontFilterCriteriaDto::instantiateFromRequest($request)->toArray(),
            $request->input('public_answers')
        );

        return $this->sendResponseForResource(
            QuestionnaireResource::collection($questionnaires),
            __("Questionnaire list retrieved successfully")
        );
    }

    public function read(QuestionnaireFrontReadRequest $request): JsonResponse
    {
        $questionnaire = $this->questionnaireService->findForFront(
            [
                'questionnaire_id' => $request->getParamId(),
                'model_type_title' => $request->getParamModelTypeTitle(),
                'model_id' => $request->getParamModelId(),
                'active' => true
            ],
            $request->user()
        );

        return $this->sendResponseForResource(
            QuestionnaireFrontResource::make($questionnaire),
            __("questionnaire fetched successfully")
        );
    }

    public function answer(QuestionnaireFrontAnswerRequest $request): JsonResponse
    {
        /** @var QuestionnaireModel $questionnaireModel */
        $questionnaireModel = QuestionnaireModel::query()->where([
            'questionnaire_id' => $request->getParamId(),
            'model_id' => $request->getParamModelId(),
            'model_type_id' => $request->getQuestionnaireModelType()->id,
        ])->firstOrFail();

        $questionnaire = $this->questionnaireAnswerService->saveAnswer(
            $questionnaireModel,
            $request->validated(),
            $request->user()
        );

        return $this->sendResponseForResource(
            QuestionnaireFrontResource::make($questionnaire),
            __("Answers save successfully")
        );
    }

    public function stars(QuestionnaireStarsFrontRequest $request): JsonResponse {
        $report = $this->questionnaireAnswerService->getStars(
            $request->getQuestionnaireModelType()->id,
            $request->getParamModelId()
        );

        return $this->sendResponseForResource(
            QuestionnaireStarsResource::make($report),
            __("Questionnaire report fetched successfully")
        );
    }

    public function questionModelAnswers(QuestionAnswersFrontReadRequest $request): JsonResponse
    {
        $answers = $this
            ->questionnaireAnswerService
            ->publicQuestionAnswers(QuestionAnswersCriteriaDto::instantiateFromRequest($request)->toArray(), $request->input('per_page'));
        return $this->sendResponseForResource(
            QuestionAnswerFrontResource::collection($answers),
            __('Question answers fetched successfully')
        );
    }

    public function modelStars(QuestionAnswersFrontStarsRequest $request): JsonResponse
    {
        $result = $this->questionnaireAnswerService->getReviewStars(QuestionAnswersCriteriaDto::instantiateFromRequest($request)->toArray());
        return $this->sendResponseForResource(
            ModelStarsResponse::make($result),
            __('Model stars fetched successfully'),
        );
    }
}
