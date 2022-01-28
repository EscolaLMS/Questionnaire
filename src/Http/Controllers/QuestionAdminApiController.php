<?php

namespace EscolaLms\Questionnaire\Http\Controllers;

use EscolaLms\Core\Http\Controllers\EscolaLmsBaseController;
use EscolaLms\Pages\Http\Controllers\Contracts\PagesAdminApiContract;
use EscolaLms\Pages\Http\Exceptions\Contracts\Renderable;
use EscolaLms\Pages\Http\Requests\QuestionnaireCreateRequest;
use EscolaLms\Pages\Http\Requests\QuestionnaireDeleteRequest;
use EscolaLms\Pages\Http\Requests\QuestionnaireListingRequest;
use EscolaLms\Pages\Http\Requests\QuestionnaireReadRequest;
use EscolaLms\Pages\Http\Requests\QuestionnaireUpdateRequest;
use EscolaLms\Pages\Http\Resources\PageResource;
use EscolaLms\Pages\Http\Services\Contracts\PageServiceContract;
use EscolaLms\Questionnaire\Http\Controllers\Contracts\QuestionAdminApiContract;
use EscolaLms\Questionnaire\Http\Controllers\Contracts\QuestionnaireAdminApiContract;
use EscolaLms\Questionnaire\Http\Requests\QuestionCreateRequest;
use EscolaLms\Questionnaire\Http\Requests\QuestionDeleteRequest;
use EscolaLms\Questionnaire\Http\Requests\QuestionListingRequest;
use EscolaLms\Questionnaire\Http\Requests\QuestionReadRequest;
use EscolaLms\Questionnaire\Http\Requests\QuestionUpdateRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class QuestionAdminApiController extends EscolaLmsBaseController implements QuestionAdminApiContract
{
    private PageServiceContract $pageService;

    public function __construct(PageServiceContract $pageService)
    {
        $this->pageService = $pageService;
    }

    public function list(QuestionListingRequest $request): JsonResponse
    {
        try {
            $pages = $this->pageService->search();
            return $this->sendResponseForResource(PageResource::collection($pages), "pages list retrieved successfully");
        } catch (Renderable $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function create(QuestionCreateRequest $request): JsonResponse
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

    public function update(QuestionUpdateRequest $request, int $id): JsonResponse
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

    public function delete(QuestionDeleteRequest $request, int $id): JsonResponse
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

    public function read(QuestionReadRequest $request, int $id): JsonResponse
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
