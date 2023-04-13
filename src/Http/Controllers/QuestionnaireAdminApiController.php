<?php

namespace EscolaLms\Questionnaire\Http\Controllers;

use EscolaLms\Core\Dtos\OrderDto;
use EscolaLms\Core\Http\Controllers\EscolaLmsBaseController;
use EscolaLms\Questionnaire\Dtos\QuestionnairesFilterCriteriaDto;
use EscolaLms\Questionnaire\Exceptions\QuestionnaireCanNotDeleteException;
use EscolaLms\Questionnaire\Http\Controllers\Contracts\QuestionnaireAdminApiContract;
use EscolaLms\Questionnaire\Http\Requests\QuestionnaireAssignUnassignRequest;
use EscolaLms\Questionnaire\Http\Requests\QuestionnaireCreateRequest;
use EscolaLms\Questionnaire\Http\Requests\QuestionnaireDeleteRequest;
use EscolaLms\Questionnaire\Http\Requests\QuestionnaireListingRequest;
use EscolaLms\Questionnaire\Http\Requests\QuestionnaireReadRequest;
use EscolaLms\Questionnaire\Http\Requests\QuestionnaireReportRequest;
use EscolaLms\Questionnaire\Http\Requests\QuestionnaireUpdateRequest;
use EscolaLms\Questionnaire\Http\Resources\QuestionnaireModelTypeCollection;
use EscolaLms\Questionnaire\Http\Resources\QuestionnaireReportCollection;
use EscolaLms\Questionnaire\Http\Resources\QuestionnaireResource;
use EscolaLms\Questionnaire\Repository\Contracts\QuestionnaireModelRepositoryContract;
use EscolaLms\Questionnaire\Repository\Contracts\QuestionnaireModelTypeRepositoryContract;
use EscolaLms\Questionnaire\Services\Contracts\QuestionnaireAnswerServiceContract;
use EscolaLms\Questionnaire\Services\Contracts\QuestionnaireServiceContract;
use Exception;
use Illuminate\Http\JsonResponse;

class QuestionnaireAdminApiController extends EscolaLmsBaseController implements QuestionnaireAdminApiContract
{
    private QuestionnaireAnswerServiceContract $questionAnswerService;
    private QuestionnaireModelTypeRepositoryContract $questionnaireModelTypeRepository;
    private QuestionnaireModelRepositoryContract $questionnaireModelRepository;
    private QuestionnaireServiceContract $questionnaireService;

    public function __construct(
        QuestionnaireAnswerServiceContract       $questionAnswerService,
        QuestionnaireModelTypeRepositoryContract $questionnaireModelTypeRepository,
        QuestionnaireModelRepositoryContract     $questionnaireModelRepository,
        QuestionnaireServiceContract             $questionnaireService
    )
    {
        $this->questionAnswerService = $questionAnswerService;
        $this->questionnaireModelTypeRepository = $questionnaireModelTypeRepository;
        $this->questionnaireModelRepository = $questionnaireModelRepository;
        $this->questionnaireService = $questionnaireService;
    }

    public function list(QuestionnaireListingRequest $request): JsonResponse
    {

        return $this->sendResponseForResource(
            QuestionnaireResource::collection(
                $this->questionnaireService->list(QuestionnairesFilterCriteriaDto::instantiateFromRequest($request)->toArray(),
                    OrderDto::instantiateFromRequest($request)
                )),
            __("Questionnaire list retrieved successfully")
        );
    }

    public function create(QuestionnaireCreateRequest $request): JsonResponse
    {
        $questionnaire = $this->questionnaireService->createQuestionnaire($request->validated());

        return $this->sendResponseForResource(
            QuestionnaireResource::make($questionnaire),
            __("Questionnaire created successfully")
        );
    }

    public function update(QuestionnaireUpdateRequest $request, int $id): JsonResponse
    {
        $updated = $this->questionnaireService->updateQuestionnaire($request->getQuestionnaire(), $request->validated());

        return $this->sendResponseForResource(
            QuestionnaireResource::make($updated),
            __("Questionnaire updated successfully")
        );
    }

    /**
     * @throws QuestionnaireCanNotDeleteException
     */
    public function delete(QuestionnaireDeleteRequest $request, int $id): JsonResponse
    {
        try {
            $this->questionnaireService->deleteQuestionnaire($request->getQuestionnaire());
        } catch (Exception $err) {
            throw new QuestionnaireCanNotDeleteException($id);
        }

        return $this->sendResponse(true, __("Questionnaire delete successfully"));
    }

    public function read(QuestionnaireReadRequest $request): JsonResponse
    {
        return $this->sendResponseForResource(
            QuestionnaireResource::make($request->getQuestionnaire()),
            __("Questionnaire fetched successfully")
        );
    }

    public function getModelsType(QuestionnaireListingRequest $request): JsonResponse
    {
        $models = $this->questionnaireModelTypeRepository->all();

        return $this->sendResponseForResource(
            QuestionnaireModelTypeCollection::make($models),
            __("Model list retrieved successfully")
        );
    }

    public function report(QuestionnaireReportRequest $request): JsonResponse
    {
        $report = $this->questionAnswerService->getReport(
            $request->getParamId(),
            $request->getParamModelTypeId(),
            $request->getParamModelId()
        );

        return $this->sendResponseForResource(
            QuestionnaireReportCollection::make($report),
            __("Questionnaire report fetched successfully")
        );
    }

    public function assign(QuestionnaireAssignUnassignRequest $request): JsonResponse
    {
        $this->questionnaireModelRepository->assignQuestionnaireModel(
            $request->getQuestionnaire(),
            $request->getQuestionnaireModelType()->getKey(),
            $request->getModelId()
        );

        return $this->sendResponse(true, __("Questionnaire model assign successfully"));
    }

    public function unassign(QuestionnaireAssignUnassignRequest $request): JsonResponse
    {
        $this->questionnaireModelRepository->unassignQuestionnaireModel(
            $request->getQuestionnaire(),
            $request->getQuestionnaireModelType()->getKey(),
            $request->getModelId()
        );

        return $this->sendResponse(true, __("Questionnaire model unassign successfully"));
    }
}
