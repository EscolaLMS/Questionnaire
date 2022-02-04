<?php

namespace EscolaLms\Questionnaire\Http\Controllers;

use EscolaLms\Core\Http\Controllers\EscolaLmsBaseController;
use EscolaLms\Questionnaire\Http\Controllers\Contracts\QuestionnaireAdminApiContract;
use EscolaLms\Questionnaire\Http\Requests\QuestionnaireCreateRequest;
use EscolaLms\Questionnaire\Http\Requests\QuestionnaireDeleteRequest;
use EscolaLms\Questionnaire\Http\Requests\QuestionnaireListingRequest;
use EscolaLms\Questionnaire\Http\Requests\QuestionnaireReadRequest;
use EscolaLms\Questionnaire\Http\Requests\QuestionnaireReportRequest;
use EscolaLms\Questionnaire\Http\Requests\QuestionnaireUpdateRequest;
use EscolaLms\Questionnaire\Http\Resources\QuestionnaireModelTypeCollection;
use EscolaLms\Questionnaire\Http\Resources\QuestionnaireReportCollection;
use EscolaLms\Questionnaire\Http\Resources\QuestionnaireResource;
use EscolaLms\Questionnaire\Models\Questionnaire;
use EscolaLms\Questionnaire\Repository\Contracts\QuestionnaireModelTypeRepositoryContract;
use EscolaLms\Questionnaire\Repository\Contracts\QuestionnaireRepositoryContract;
use EscolaLms\Questionnaire\Services\Contracts\QuestionnaireAnswerServiceContract;
use EscolaLms\Questionnaire\Services\Contracts\QuestionnaireServiceContract;
use Illuminate\Http\JsonResponse;

class QuestionnaireAdminApiController extends EscolaLmsBaseController implements QuestionnaireAdminApiContract
{
    private QuestionnaireRepositoryContract $questionnaireRepository;
    private QuestionnaireAnswerServiceContract $questionAnswerService;
    private QuestionnaireModelTypeRepositoryContract $questionnaireModelTypeRepository;
    private QuestionnaireServiceContract $questionnaireService;

    public function __construct(
        QuestionnaireRepositoryContract $questionnaireRepository,
        QuestionnaireAnswerServiceContract $questionAnswerService,
        QuestionnaireModelTypeRepositoryContract $questionnaireModelTypeRepository,
        QuestionnaireServiceContract $questionnaireService
    ) {
        $this->questionnaireRepository = $questionnaireRepository;
        $this->questionAnswerService = $questionAnswerService;
        $this->questionnaireModelTypeRepository = $questionnaireModelTypeRepository;
        $this->questionnaireService = $questionnaireService;
    }

    public function list(QuestionnaireListingRequest $request): JsonResponse
    {
        $questionnaires = $this->questionnaireRepository->searchAndPaginate();

        return $this->sendResponseForResource(
            QuestionnaireResource::collection($questionnaires),
            __("Questionnaire list retrieved successfully")
        );
    }

    public function create(QuestionnaireCreateRequest $request): JsonResponse
    {
        $questionnaire = new Questionnaire([
            'title' => $request->getParamTitle(),
            'active' => $request->getParamActive(),
        ]);

        $questionnaire = $this->questionnaireRepository->insert($questionnaire);

        return $this->sendResponseForResource(
            QuestionnaireResource::make($questionnaire),
            __("Questionnaire created successfully")
        );
    }

    public function update(QuestionnaireUpdateRequest $request, int $id): JsonResponse
    {
        $input = $request->all();
        $updated = $this->questionnaireRepository->update($input, $id);

        return $this->sendResponseForResource(
            QuestionnaireResource::make($updated),
            __("Questionnaire updated successfully")
        );
    }

    public function delete(QuestionnaireDeleteRequest $request, int $id): JsonResponse
    {
        $deleted = $this->questionnaireService->deleteQuestionnaire($request->getQuestionnaire());
        if (!$deleted) {
            return $this->sendError(__("Can't delete questionnaire"), 404);
        }

        return $this->sendResponse(true, __("Questionnaire delete successfully"));
    }

    public function read(QuestionnaireReadRequest $request, int $id): JsonResponse
    {
        $questionnaire = $this->questionnaireRepository->find($id);

        return $this->sendResponseForResource(
            QuestionnaireResource::make($questionnaire),
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

    public function report(QuestionnaireReportRequest $request): JsonResponse {
        $report = $this->questionAnswerService->getReport(
            $request->getParamId(),
            $request->getParamModelTypeId(),
            $request->getParamModelId(),
            $request->getParamUserId()
        );

        return $this->sendResponseForResource(
            QuestionnaireReportCollection::make($report),
            __("Questionnaire report fetched successfully")
        );
    }
}
