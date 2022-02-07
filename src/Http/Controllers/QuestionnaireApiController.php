<?php

namespace EscolaLms\Questionnaire\Http\Controllers;

use EscolaLms\Core\Http\Controllers\EscolaLmsBaseController;
use EscolaLms\Questionnaire\Http\Controllers\Contracts\QuestionnaireApiContract;
use EscolaLms\Questionnaire\Http\Requests\QuestionnaireFrontAnswerRequest;
use EscolaLms\Questionnaire\Http\Requests\QuestionnaireFrontListingRequest;
use EscolaLms\Questionnaire\Http\Requests\QuestionnaireFrontReadRequest;
use EscolaLms\Questionnaire\Http\Resources\QuestionnaireFrontResource;
use EscolaLms\Questionnaire\Http\Resources\QuestionnaireResource;
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
            [
                'model_type_title' => $request->getParamModelTypeTitle(),
                'model_id' => $request->getParamModelId(),
                'active' => true
            ],
            $request->user()
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
                'id' => $request->getParamId(),
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

        $questionnaire = $this->questionnaireAnswerService->saveAnswers(
            $questionnaireModel,
            $request->validated(),
            $request->user()
        );

        return $this->sendResponseForResource(
            QuestionnaireFrontResource::make($questionnaire),
            __("Answers save successfully")
        );
    }
}
