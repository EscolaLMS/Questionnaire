<?php

namespace EscolaLms\Questionnaire\Http\Controllers\Contracts;

use EscolaLms\Questionnaire\Http\Requests\QuestionAnswerListingRequest;
use Illuminate\Http\JsonResponse;

interface QuestionAnswerAdminApiContract
{
    /**
     * @OA\Get(
     *     path="/api/admin/question-answers/{id}",
     *     summary="Lists questions answers for questionaire",
     *     tags={"QuestionAnswersForQuestionaire"},
     *     security={
     *         {"passport": {}},
     *     },
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
     *         name="questionnaire_model_id",
     *         description="id of questionnaire model",
     *         @OA\Schema(
     *            type="integer",
     *         ),
     *         required=false
     *     ),
     *     @OA\Parameter(
     *         name="question_id",
     *         description="id of question",
     *         @OA\Schema(
     *            type="integer",
     *         ),
     *         required=false
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lists questions answers for questionaire",
     *         @OA\MediaType(
     *            mediaType="application/json",
     *            @OA\Schema(
     *                type="object",
     *                description="map of questions",
     *                @OA\AdditionalProperties(
     *                    ref="#/components/schemas/QuestionAnswer"
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
    public function list(QuestionAnswerListingRequest $request): JsonResponse;
}
