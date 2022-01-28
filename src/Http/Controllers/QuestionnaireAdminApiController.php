<?php

namespace EscolaLms\Questionnaire\Http\Controllers;

use EscolaLms\Core\Http\Controllers\EscolaLmsBaseController;
use EscolaLms\Pages\Http\Controllers\Contracts\PagesAdminApiContract;
use EscolaLms\Pages\Http\Exceptions\Contracts\Renderable;
use EscolaLms\Pages\Http\Resources\PageResource;
use EscolaLms\Pages\Http\Services\Contracts\PageServiceContract;
use EscolaLms\Questionnaire\Http\Controllers\Contracts\QuestionnaireAdminApiContract;
use EscolaLms\Questionnaire\Http\Requests\QuestionnaireCreateRequest;
use EscolaLms\Questionnaire\Http\Requests\QuestionnaireDeleteRequest;
use EscolaLms\Questionnaire\Http\Requests\QuestionnaireListingRequest;
use EscolaLms\Questionnaire\Http\Requests\QuestionnaireReadRequest;
use EscolaLms\Questionnaire\Http\Requests\QuestionnaireUpdateRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class QuestionnaireAdminApiController extends EscolaLmsBaseController implements QuestionnaireAdminApiContract
{
    private PageServiceContract $pageService;

    public function __construct(PageServiceContract $pageService)
    {
        $this->pageService = $pageService;
    }

    public function list(QuestionnaireListingRequest $request): JsonResponse
    {
        try {
            $pages = $this->pageService->search();
            return $this->sendResponseForResource(PageResource::collection($pages), "pages list retrieved successfully");
        } catch (Renderable $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function create(QuestionnaireCreateRequest $request): JsonResponse
    {
        try {
            $slug = $request->getParamSlug();
            $title = $request->getParamTitle();
            $content = $request->getParamContent();
            $active = $request->get('active');

            $user = Auth::user();
            $page = $this->pageService->insert($slug, $title, $content, $user->id, $active);
            return $this->sendResponseForResource(PageResource::make($page), "page created successfully");
        } catch (Renderable $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function update(QuestionnaireUpdateRequest $request, int $id): JsonResponse
    {
        try {
            $input = $request->all();

            $updated = $this->pageService->update($id, $input);
            if (!$updated) {
                return $this->sendError(sprintf("Page with slug '%s' doesn't exists", $id), 404);
            }
            return $this->sendResponseForResource(PageResource::make($updated), "page updated successfully");
        } catch (Renderable $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function delete(QuestionnaireDeleteRequest $request, int $id): JsonResponse
    {
        try {
            $deleted = $this->pageService->deleteById($id);
            if (!$deleted) {
                return $this->sendError(sprintf("Page with id '%s' doesn't exists", $id), 404);
            }
            return $this->sendResponse($deleted, "page updated successfully");
        } catch (Renderable $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function read(QuestionnaireReadRequest $request, int $id): JsonResponse
    {
        try {
            $page = $this->pageService->getById($id);
            if ($page->exists) {
                return $this->sendResponseForResource(PageResource::make($page), "page fetched successfully");
            }
            return $this->sendError(sprintf("Page with id '%s' doesn't exists", $id), 404);
        } catch (Renderable $e) {
            return $this->sendError($e->getMessage());
        }
    }
}
