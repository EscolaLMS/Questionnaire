<?php

namespace EscolaLms\Questionnaire\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="ModelStarsResponse",
 *     @OA\Property(
 *          property="avg_rate",
 *          type="string",
 *          description="average rate"
 *     ),
 *     @OA\Property(
 *         property="count_answers",
 *         type="integer",
 *         description="count all answers for this question"
 *     ),
 *     @OA\Property(
 *         property="count_public_answers",
 *         type="integer",
 *         description="count all public answers for this question"
 *     ),
 *     @OA\Property(
 *         property="question_id",
 *         type="integer",
 *         description="question identifier"
 *     ),
 * )
 */
class ModelStarsResponse extends JsonResource
{
    public function toArray($request)
    {
        return [
            'avg_rate' => $this['avg_rate'] ? (float) $this['avg_rate'] : null,
            'count_answers' => $this['count_answers'] ?? null,
            'question_id' => $this['question_id'] ?? null,
            'count_public_answers' => $this['count_public_answers'] ?? null,
        ];
    }
}
