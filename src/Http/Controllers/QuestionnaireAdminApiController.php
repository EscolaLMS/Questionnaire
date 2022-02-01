<?php

namespace EscolaLms\Questionnaire\Http\Controllers;

use EscolaLms\Core\Http\Controllers\EscolaLmsBaseController;
use EscolaLms\Questionnaire\Enums\ModelEnum;
use EscolaLms\Questionnaire\Http\Resources\QuestionnaireResource;
use EscolaLms\Questionnaire\Http\Controllers\Contracts\QuestionnaireAdminApiContract;
use EscolaLms\Questionnaire\Http\Requests\QuestionnaireCreateRequest;
use EscolaLms\Questionnaire\Http\Requests\QuestionnaireDeleteRequest;
use EscolaLms\Questionnaire\Http\Requests\QuestionnaireListingRequest;
use EscolaLms\Questionnaire\Http\Requests\QuestionnaireReadRequest;
use EscolaLms\Questionnaire\Http\Requests\QuestionnaireUpdateRequest;
use EscolaLms\Questionnaire\Models\Questionnaire;
use EscolaLms\Questionnaire\Repository\Contracts\QuestionnaireRepositoryContract;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionnaireAdminApiController extends EscolaLmsBaseController implements QuestionnaireAdminApiContract
{
    private QuestionnaireRepositoryContract $questionnaireRepository;

    public function __construct(QuestionnaireRepositoryContract $questionnaireRepository)
    {
        $this->questionnaireRepository = $questionnaireRepository;
    }

    public function list(QuestionnaireListingRequest $request): JsonResponse
    {
        try {
            $questionnaires = $this->questionnaireRepository->searchAndPaginate();
            return $this->sendResponseForResource(
                QuestionnaireResource::collection($questionnaires),
                "Questionnaire list retrieved successfully"
            );
        } catch (Renderable $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function create(QuestionnaireCreateRequest $request): JsonResponse
    {
        try {
            /** @var Questionnaire $questionnaire */
            $questionnaire = Questionnaire::factory()->newModel([
                'title' => $request->getParamTitle(),
                'model' => $request->getParamModel(),
                'model_id' => $request->getParamModelId(),
                'active' => $request->get('active') ?? true,
            ]);

            $questionnaire = $this->questionnaireRepository->insert($questionnaire);
            return $this->sendResponseForResource(
                QuestionnaireResource::make($questionnaire),
                "Questionnaire created successfully"
            );
        } catch (Renderable $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function update(QuestionnaireUpdateRequest $request, int $id): JsonResponse
    {
        try {
            $input = $request->all();

            $updated = $this->questionnaireRepository->update($input, $id);
            if (!$updated) {
                return $this->sendError(sprintf("Questionnaire with slug '%s' doesn't exists", $id), 404);
            }
            return $this->sendResponseForResource(
                QuestionnaireResource::make($updated),
                "Questionnaire updated successfully"
            );
        } catch (Renderable $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function delete(QuestionnaireDeleteRequest $request, int $id): JsonResponse
    {
        try {
            $deleted = $this->questionnaireRepository->deleteQuestionnaire($id);
            if (!$deleted) {
                return $this->sendError(sprintf("Questionnaire with id '%s' doesn't exists", $id), 404);
            }
            return $this->sendResponse($deleted, "Questionnaire delete successfully");
        } catch (Renderable $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function read(QuestionnaireReadRequest $request, int $id): JsonResponse
    {
        try {
            $questionnaire = $this->questionnaireRepository->find($id);
            if ($questionnaire->exists) {
                return $this->sendResponseForResource(
                    QuestionnaireResource::make($questionnaire),
                    "Questionnaire fetched successfully"
                );
            }
            return $this->sendError(sprintf("Questionnaire with id '%s' doesn't exists", $id), 404);
        } catch (Renderable $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function models(QuestionnaireListingRequest $request): JsonResponse
    {
        try {
            return $this->sendResponseForResource(
                JsonResource::collection(ModelEnum::getValues()),
                "Model list retrieved successfully"
            );
        } catch (Renderable $e) {
            return $this->sendError($e->getMessage());
        }
    }
}
