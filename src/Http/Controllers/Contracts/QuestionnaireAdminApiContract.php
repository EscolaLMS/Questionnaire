<?php

namespace EscolaLms\Questionnaire\Http\Controllers\Contracts;

use EscolaLms\Questionnaire\Http\Requests\QuestionnaireCreateRequest;
use EscolaLms\Questionnaire\Http\Requests\QuestionnaireDeleteRequest;
use EscolaLms\Questionnaire\Http\Requests\QuestionnaireListingRequest;
use EscolaLms\Questionnaire\Http\Requests\QuestionnaireReadRequest;
use EscolaLms\Questionnaire\Http\Requests\QuestionnaireReportRequest;
use EscolaLms\Questionnaire\Http\Requests\QuestionnaireUpdateRequest;
use Illuminate\Http\JsonResponse;

interface QuestionnaireAdminApiContract
{
    /**
     * @OA\Get(
     *     path="/api/admin/questionnaire",
     *     summary="Lists available questionnaires",
     *     tags={"Questionnaire"},
     *     security={
     *         {"passport": {}},
     *     },
     *     @OA\Response(
     *         response=200,
     *         description="list of available questionnaires",
     *         @OA\MediaType(
     *            mediaType="application/json",
     *            @OA\Schema(
     *                type="object",
     *                description="map of questionnaires",
     *                @OA\AdditionalProperties(
     *                    ref="#/components/schemas/Questionnaire"
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
     */
    public function list(QuestionnaireListingRequest $request): JsonResponse;

    /**
     * @OA\Post(
     *     path="/api/admin/questionnaire",
     *     summary="Create a new questionnaire identified by id",
     *     tags={"Questionnaire"},
     *     security={
     *         {"passport": {}},
     *     },
     *     @OA\RequestBody(
     *         description="Questionnaire attributes",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Questionnaire")
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Questionnaire created successfully",
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
     *          description="there already is a questionnaire",
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
     */
    public function create(QuestionnaireCreateRequest $request): JsonResponse;

    /**
     * @OA\Patch(
     *     path="/api/admin/questionnaire/{id}",
     *     summary="Update an existing questionnaire identified by id",
     *     tags={"Questionnaire"},
     *     security={
     *         {"passport": {}},
     *     },
     *     @OA\Parameter(
     *         description="Unique human-readable questionnaire identifier",
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         description="Questionnaire attributes",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Questionnaire")
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Questionnaire updated successfully",
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
     *          description="cannot find a questionnaire",
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
     */
    public function update(QuestionnaireUpdateRequest $request, int $id): JsonResponse;

    /**
     * @OA\Delete(
     *     path="/api/admin/questionnaire/{id}",
     *     summary="Delete a questionnaire identified by a id",
     *     tags={"Questionnaire"},
     *     security={
     *         {"passport": {}},
     *     },
     *     @OA\Parameter(
     *         description="Unique human-readable questionnaire identifier",
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Questionnaire deleted successfully",
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
     *          description="cannot find a questionnaire",
     *      ),
     *     @OA\Response(
     *          response=500,
     *          description="server-side error",
     *      ),
     * )
     */
    public function delete(QuestionnaireDeleteRequest $request, int $id): JsonResponse;

    /**
     * @OA\Get(
     *     path="/api/admin/questionnaire/{id}",
     *     summary="Read a questionnaire identified by a given id identifier",
     *     tags={"Questionnaire"},
     *     security={
     *         {"passport": {}},
     *     },
     *     @OA\Parameter(
     *         description="Unique human-readable questionnaire identifier",
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="",
     *         @OA\JsonContent(ref="#/components/schemas/Questionnaire")
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
     */
    public function read(QuestionnaireReadRequest $request, int $id): JsonResponse;

    /**
     * @OA\Get(
     *     path="/api/admin/questionnaire/model",
     *     summary="Lists available questionnaire model type",
     *     tags={"QuestionnaireModelType"},
     *     security={
     *         {"passport": {}},
     *     },
     *     @OA\Response(
     *         response=200,
     *         description="list of available questionnaire model type",
     *         @OA\MediaType(
     *            mediaType="application/json",
     *            @OA\Schema(
     *                type="object",
     *                description="map of questionnaire model type",
     *                @OA\AdditionalProperties(
     *                    ref="#/components/schemas/QuestionnaireModelType"
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
     */
    public function getModelsType(QuestionnaireListingRequest $request): JsonResponse;

    /**
     * @OA\Get(
     *     path="/api/admin/questionnaire/report/{id}",
     *     summary="Read a questionnaire report identified by a given id identifier",
     *     tags={"QuestionnaireReport"},
     *     security={
     *         {"passport": {}},
     *     },
     *     @OA\Parameter(
     *         description="Unique human-readable questionnaire identifier",
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="",
     *         @OA\JsonContent(ref="#/components/schemas/QuestionnaireReportResource")
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
     */
    public function report(
        QuestionnaireReportRequest $request,
        int $id,
        ?int $model_type_id = null,
        ?int $model_id = null,
        ?int $user_id = null
    ): JsonResponse;
}
