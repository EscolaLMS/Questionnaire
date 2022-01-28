<?php

namespace EscolaLms\Questionnaire\Http\Controllers\Contracts;

use EscolaLms\Questionnaire\Http\Requests\QuestionnaireCreateRequest;
use EscolaLms\Questionnaire\Http\Requests\QuestionnaireDeleteRequest;
use EscolaLms\Questionnaire\Http\Requests\QuestionnaireListingRequest;
use EscolaLms\Questionnaire\Http\Requests\QuestionnaireReadRequest;
use EscolaLms\Questionnaire\Http\Requests\QuestionnaireUpdateRequest;
use Illuminate\Http\JsonResponse;

interface QuestionnaireAdminApiContract
{
    /**
     * @OA\Get(
     *     path="/api/admin/pages",
     *     summary="Lists available pages",
     *     tags={"Pages"},
     *     security={
     *         {"passport": {}},
     *     },
     *     @OA\Response(
     *         response=200,
     *         description="list of available pages",
     *         @OA\MediaType(
     *            mediaType="application/json",
     *            @OA\Schema(
     *                type="object",
     *                description="map of pages identified by a slug value",
     *                @OA\AdditionalProperties(
     *                    ref="#/components/schemas/Page"
     *                )
     *            )
     *         )
     *      ),
     *     @OA\Response(
     *          response=401,
     *          description="endpoint requires authentication",
     *     ),
     *     @OA\Response(
     *          response=403,
     *          description="user doesn't have required access rights",
     *      ),
     *     @OA\Response(
     *          response=500,
     *          description="server-side error",
     *      ),
     * )
     *
     * @param QuestionnaireListingRequest $request
     * @return JsonResponse
     */
    public function list(QuestionnaireListingRequest $request): JsonResponse;

    /**
     * @OA\Post(
     *     path="/api/admin/pages",
     *     summary="Create a new page identified by id",
     *     tags={"Pages"},
     *     security={
     *         {"passport": {}},
     *     },
     *     @OA\RequestBody(
     *         description="Page attributes",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Page")
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="page created successfully",
     *      ),
     *     @OA\Response(
     *          response=401,
     *          description="endpoint requires authentication",
     *      ),
     *     @OA\Response(
     *          response=403,
     *          description="user doesn't have required access rights",
     *      ),
     *     @OA\Response(
     *          response=409,
     *          description="there already is a page identified by chosen slug identifier",
     *      ),
     *     @OA\Response(
     *          response=422,
     *          description="one of the parameters has invalid format",
     *      ),
     *     @OA\Response(
     *          response=500,
     *          description="server-side error",
     *      ),
     * )
     *
     * @param QuestionnaireCreateRequest $request
     * @return JsonResponse
     */
    public function create(QuestionnaireCreateRequest $request): JsonResponse;

    /**
     * @OA\Patch(
     *     path="/api/admin/pages/{id}",
     *     summary="Update an existing page identified by id",
     *     tags={"Pages"},
     *     security={
     *         {"passport": {}},
     *     },
     *     @OA\Parameter(
     *         description="Unique human-readable page identifier",
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         description="Page attributes",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Page")
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="page updated successfully",
     *      ),
     *     @OA\Response(
     *          response=401,
     *          description="endpoint requires authentication",
     *      ),
     *     @OA\Response(
     *          response=403,
     *          description="user doesn't have required access rights",
     *      ),
     *     @OA\Response(
     *          response=400,
     *          description="cannot find a page with provided slug identifier",
     *      ),
     *     @OA\Response(
     *          response=422,
     *          description="one of the parameters has invalid format",
     *      ),
     *     @OA\Response(
     *          response=500,
     *          description="server-side error",
     *      ),
     * )
     *
     * @param QuestionnaireUpdateRequest $request
     * @return JsonResponse
     */
    public function update(QuestionnaireUpdateRequest $request, int $id): JsonResponse;

    /**
     * @OA\Delete(
     *     path="/api/admin/pages/{id}",
     *     summary="Delete a page identified by a id",
     *     tags={"Pages"},
     *     security={
     *         {"passport": {}},
     *     },
     *     @OA\Parameter(
     *         description="Unique human-readable page identifier",
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="page deleted successfully",
     *      ),
     *     @OA\Response(
     *          response=401,
     *          description="endpoint requires authentication",
     *      ),
     *     @OA\Response(
     *          response=403,
     *          description="user doesn't have required access rights",
     *      ),
     *     @OA\Response(
     *          response=400,
     *          description="cannot find a page with provided slug identifier",
     *      ),
     *     @OA\Response(
     *          response=500,
     *          description="server-side error",
     *      ),
     * )
     *
     * @param QuestionnaireDeleteRequest $request
     * @return JsonResponse
     */
    public function delete(QuestionnaireDeleteRequest $request, int $id): JsonResponse;

    /**
     * @OA\Get(
     *     path="/api/admin/pages/{id}",
     *     summary="Read a page identified by a given id identifier",
     *     tags={"Pages"},
     *     security={
     *         {"passport": {}},
     *     },
     *     @OA\Parameter(
     *         description="Unique human-readable page identifier",
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="",
     *         @OA\JsonContent(ref="#/components/schemas/Page")
     *      ),
     *     @OA\Response(
     *          response=401,
     *          description="endpoint requires authentication",
     *     ),
     *     @OA\Response(
     *          response=403,
     *          description="user doesn't have required access rights",
     *      ),
     *     @OA\Response(
     *          response=500,
     *          description="server-side error",
     *      ),
     * )
     *
     * @param QuestionnaireReadRequest $request
     * @return JsonResponse
     */
    public function read(QuestionnaireReadRequest $request, int $id): JsonResponse;
}
