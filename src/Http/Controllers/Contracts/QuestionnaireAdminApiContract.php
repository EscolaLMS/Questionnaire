<?php

namespace EscolaLms\Questionnaire\Http\Controllers\Contracts;

use EscolaLms\Questionnaire\Http\Requests\QuestionnaireAssignUnassignRequest;
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
     *     @OA\Parameter(
     *          name="page",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="integer",
     *          ),
     *      ),
     *     @OA\Parameter(
     *          name="per_page",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="integer",
     *          ),
     *      ),
     *      @OA\Parameter(
     *          name="order_by",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="string",
     *              enum={"id", "created_at", "title"}
     *          ),
     *      ),
     *      @OA\Parameter(
     *          name="order",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="string",
     *              enum={"ASC", "DESC"}
     *          ),
     *      ),
     *      @OA\Parameter(
     *          name="title",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="string",
     *          ),
     *      ),
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
     *     summary="Create a new questionnaire",
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
    public function read(QuestionnaireReadRequest $request): JsonResponse;

    /**
     * @OA\Get(
     *     path="/api/admin/questionnaire-model",
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
     *         description="questionnaire identifier",
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
    /**
     * @OA\Get(
     *     path="/api/admin/questionnaire/report/{id}/{model_type_id}",
     *     summary="Read a questionnaire report identified by a given id identifier and model type",
     *     tags={"QuestionnaireReportForModelType"},
     *     security={
     *         {"passport": {}},
     *     },
     *     @OA\Parameter(
     *         description="questionnaire identifier",
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="questionnaire model type identified",
     *         in="path",
     *         name="model_type_id",
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
    /**
     * @OA\Get(
     *     path="/api/admin/questionnaire/report/{id}/{model_type_id}/{model_id}",
     *     summary="Read a questionnaire report identified by a given id identifier and model type and model identified",
     *     tags={"QuestionnaireReportForModel"},
     *     security={
     *         {"passport": {}},
     *     },
     *     @OA\Parameter(
     *         description="questionnaire identifier",
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="questionnaire model type identified",
     *         in="path",
     *         name="model_type_id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="model identifier",
     *         in="path",
     *         name="model_id",
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
    public function report(QuestionnaireReportRequest $request): JsonResponse;

    /**
     * @OA\Patch(
     *     path="/api/admin/questionnaire/assign/{model_type_title}/{model_id}/{id}/{target_group}",
     *     summary="assign a questionnaire model",
     *     tags={"Questionnaire"},
     *     security={
     *         {"passport": {}},
     *     },
     *    @OA\Parameter(
     *         name="model_type_title",
     *         description="Name of Model (Course, Webinar etd.)",
     *         @OA\Schema(
     *            type="string",
     *         ),
     *         required=true,
     *         in="path"
     *     ),
     *     @OA\Parameter(
     *         name="model_id",
     *         description="id of Model (Course, Webinar etd.)",
     *         @OA\Schema(
     *            type="integer",
     *         ),
     *         required=true,
     *         in="path"
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         description="id of questionnaire",
     *         @OA\Schema(
     *            type="integer",
     *         ),
     *         required=true,
     *         in="path"
     *     ),
     *     @OA\Parameter(
     *         name="target_group",
     *         description="Target Group: user or author",
     *         @OA\Schema(
     *            type="string",
     *         ),
     *         required=false,
     *         in="path"
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="display_frequency_minutes",
     *                 type="integer",
     *                 description="Display frequency in minutes"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Questionnaire model assign successfully",
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
    public function assign(QuestionnaireAssignUnassignRequest $request): JsonResponse;

    /**
     * @OA\Delete(
     *     path="/api/admin/questionnaire/unassign/{model_type_title}/{model_id}/{id}/{target_group}",
     *     summary="unassign a questionnaire model",
     *     tags={"Questionnaire"},
     *     security={
     *         {"passport": {}},
     *     },
     *    @OA\Parameter(
     *         name="model_type_title",
     *         description="Name of Model (Course, Webinar etd.)",
     *         @OA\Schema(
     *            type="string",
     *         ),
     *         required=true,
     *         in="path"
     *     ),
     *     @OA\Parameter(
     *         name="model_id",
     *         description="id of Model (Course, Webinar etd.)",
     *         @OA\Schema(
     *            type="integer",
     *         ),
     *         required=true,
     *         in="path"
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         description="id of questionnaire",
     *         @OA\Schema(
     *            type="integer",
     *         ),
     *         required=true,
     *         in="path"
     *     ),
     *     @OA\Parameter(
     *         name="target_group",
     *         description="Target Group: user or author",
     *         @OA\Schema(
     *            type="string",
     *         ),
     *         required=false,
     *         in="path"
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Questionnaire model unassign successfully",
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
    public function unassign(QuestionnaireAssignUnassignRequest $request): JsonResponse;
}
