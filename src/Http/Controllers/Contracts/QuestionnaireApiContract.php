<?php

namespace EscolaLms\Questionnaire\Http\Controllers\Contracts;

use EscolaLms\Questionnaire\Http\Requests\QuestionnaireFrontListingRequest;
use EscolaLms\Questionnaire\Http\Requests\QuestionnaireFrontReadRequest;
use Illuminate\Http\JsonResponse;

interface QuestionnaireApiContract
{
    /**
     * @OA\Get(
     *     path="/api/questionnaire",
     *     summary="Lists available questionnaires",
     *     tags={"Questionnaire"},
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
    public function list(QuestionnaireFrontListingRequest $request): JsonResponse;

    /**
     * @OA\Get(
     *     path="/api/questionnaire/{slug}",
     *     summary="Read a questionnaire identified by a given slug identifier",
     *     tags={"Questionnaire"},
     *     @OA\Parameter(
     *         description="Unique human-readable questionnaire identifier",
     *         in="path",
     *         name="slug",
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
    public function read(QuestionnaireFrontReadRequest $request): JsonResponse;
}
