<?php

namespace EscolaLms\Questionnaire\Http\Controllers;

use EscolaLms\Core\Http\Controllers\EscolaLmsBaseController;
use EscolaLms\Pages\Http\Controllers\Contracts\PagesApiContract;
use EscolaLms\Pages\Http\Exceptions\Contracts\Renderable;
use EscolaLms\Pages\Http\Resources\PageResource;
use EscolaLms\Pages\Http\Services\Contracts\PageServiceContract;
use EscolaLms\Questionnaire\Http\Controllers\Contracts\QuestionnaireApiContract;
use EscolaLms\Questionnaire\Http\Requests\QuestionnaireFrontListingRequest;
use EscolaLms\Questionnaire\Http\Requests\QuestionnaireFrontReadRequest;
use Illuminate\Http\JsonResponse;

class QuestionnaireApiController extends EscolaLmsBaseController implements QuestionnaireApiContract
{
    private PageServiceContract $pageService;

    public function __construct(PageServiceContract $pageService)
    {
        $this->pageService = $pageService;
    }

    public function list(QuestionnaireFrontListingRequest $request): JsonResponse
    {
        try {
            $pages = $this->pageService->search(['active' => true]);
            return $this->sendResponseForResource(PageResource::collection($pages), "pages list retrieved successfully");
        } catch (Renderable $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function read(QuestionnaireFrontReadRequest $request): JsonResponse
    {
        try {
            $slug = $request->getParamSlug();
            $page = $this->pageService->getBySlug($slug);
            if ($page->exists) {
                if (!$page->active) {
                    return $this->sendError(sprintf("You don't have access to page with slug '%s'", $slug), 403);
                }
                return $this->sendResponseForResource(PageResource::make($page), "page fetched successfully");
            }
            return $this->sendError(sprintf("Page with slug '%s' doesn't exists", $slug), 404);
        } catch (Renderable $e) {
            return $this->sendError($e->getMessage());
        }
    }
}
