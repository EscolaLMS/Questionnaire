<?php

namespace EscolaLms\Questionnaire\Http\Controllers\Contracts;

use EscolaLms\Questionnaire\Http\Requests\QuestionAnswersFrontReadRequest;
use EscolaLms\Questionnaire\Http\Requests\QuestionnaireFrontAnswerRequest;
use EscolaLms\Questionnaire\Http\Requests\QuestionnaireFrontListingRequest;
use EscolaLms\Questionnaire\Http\Requests\QuestionnaireFrontReadRequest;
use EscolaLms\Questionnaire\Http\Requests\QuestionnaireStarsFrontRequest;
use Illuminate\Http\JsonResponse;

interface QuestionnaireApiContract
{
    /**
     * @OA\Get(
     *     path="/api/questionnaire/{model_type_title}/{model_id}",
     *     summary="Lists available questionnaires",
     *     tags={"Questionnaire"},
     *     security={
     *         {"passport": {}},
     *     },
     *     @OA\Parameter(
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
     *     path="/api/questionnaire/{model_type_title}/{model_id}/{id}",
     *     summary="Read a questionnaire identified by a given id identifier and model",
     *     tags={"Questionnaire"},
     *     security={
     *         {"passport": {}},
     *     },
     *     @OA\Parameter(
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
     *         description="id of Questionnaire",
     *         @OA\Schema(
     *            type="integer",
     *         ),
     *         required=true,
     *         in="path"
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

    /**
     * @OA\Post(
     *     path="/api/questionnaire/{model_type_title}/{model_id}/{id}",
     *     summary="Save a questionnaire answers",
     *     tags={"Questionnaire"},
     *     security={
     *         {"passport": {}},
     *     },
     *     @OA\Parameter(
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
     *     @OA\RequestBody(
     *         description="Answer attributes",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/QuestionnaireAnswerRequest")
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
    public function answer(QuestionnaireFrontAnswerRequest $request): JsonResponse;

    /**
     * @OA\Get(
     *     path="/api/questionnaire/stars/{model_type_title}/{model_id}",
     *     summary="Read a questionnaire stars identified by a given model title and model id identifier",
     *     tags={"QuestionnaireStars"},
     *     @OA\Parameter(
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
     *     @OA\Response(
     *         response=200,
     *         description="",
     *         @OA\JsonContent(ref="#/components/schemas/QuestionnaireStarsResource")
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
    public function stars(QuestionnaireStarsFrontRequest $request): JsonResponse;

    /**
     * @OA\Get(
     *     path="/api/questionnaire/{model_type_title}/{model_id}/questions/{question_id}/answers",
     *     summary="Read a question answers by a given model title and model id identifier",
     *     tags={"Questionnaire"},
     *     @OA\Parameter(
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
     *         name="question_id",
     *         description="id of Question",
     *         @OA\Schema(
     *            type="integer",
     *         ),
     *         required=true,
     *         in="path"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="",
     *         @OA\JsonContent(ref="#/components/schemas/QuestionAnswer")
     *      ),
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
    public function questionModelAnswers(QuestionAnswersFrontReadRequest $request): JsonResponse;
}
