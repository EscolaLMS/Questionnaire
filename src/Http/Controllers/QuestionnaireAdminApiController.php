<?php

namespace EscolaLms\Questionnaire\Http\Controllers;

use EscolaLms\Core\Http\Controllers\EscolaLmsBaseController;
use EscolaLms\Questionnaire\Enums\ModelEnum;
use EscolaLms\Questionnaire\Http\Controllers\Contracts\QuestionnaireAdminApiContract;
use EscolaLms\Questionnaire\Http\Requests\QuestionnaireCreateRequest;
use EscolaLms\Questionnaire\Http\Requests\QuestionnaireDeleteRequest;
use EscolaLms\Questionnaire\Http\Requests\QuestionnaireListingRequest;
use EscolaLms\Questionnaire\Http\Requests\QuestionnaireReadRequest;
use EscolaLms\Questionnaire\Http\Requests\QuestionnaireReportRequest;
use EscolaLms\Questionnaire\Http\Requests\QuestionnaireUpdateRequest;
use EscolaLms\Questionnaire\Http\Resources\QuestionnaireReportCollection;
use EscolaLms\Questionnaire\Http\Resources\QuestionnaireReportResource;
use EscolaLms\Questionnaire\Http\Resources\QuestionnaireResource;
use EscolaLms\Questionnaire\Models\Questionnaire;
use EscolaLms\Questionnaire\Repository\Contracts\QuestionnaireRepositoryContract;
use EscolaLms\Questionnaire\Services\Contracts\QuestionnaireAnswerServiceContract;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionnaireAdminApiController extends EscolaLmsBaseController implements QuestionnaireAdminApiContract
{
    private QuestionnaireRepositoryContract $questionnaireRepository;
    private QuestionnaireAnswerServiceContract $questionAnswerService;

    public function __construct(
        QuestionnaireRepositoryContract $questionnaireRepository,
        QuestionnaireAnswerServiceContract $questionAnswerService
    ) {
        $this->questionnaireRepository = $questionnaireRepository;
        $this->questionAnswerService = $questionAnswerService;
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
        $this->questionnaireRepository->deleteQuestionnaire($id);

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

    public function models(QuestionnaireListingRequest $request): JsonResponse
    {
        return $this->sendResponseForResource(
            JsonResource::collection(ModelEnum::getValues()),
            __("Model list retrieved successfully")
        );
    }

    public function report(
        QuestionnaireReportRequest $request,
        int $id,
        ?int $model_type_id = null,
        ?int $model_id = null,
        ?int $user_id = null
    ): JsonResponse {
        $report = $this->questionAnswerService->getReport($id, $model_type_id, $model_id, $user_id);

        return $this->sendResponseForResource(
            QuestionnaireReportCollection::make($report),
            //QuestionnaireReportResource::make($report),
            __("Questionnaire report fetched successfully")
        );
    }
}
