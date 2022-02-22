<?php

namespace EscolaLms\Questionnaire\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="QuestionnaireStarsResource",
 *     @OA\Property(
 *          property="sum_rate",
 *          type="string",
 *          description="sum of rates for this question"
 *     ),
 *     @OA\Property(
 *         property="count_answers",
 *         type="integer",
 *         description="count all answers for this question"
 *     ),
 *     @OA\Property(
 *          property="avg_rate",
 *          type="string",
 *          description="average rate"
 *     ),
 * )
 */
class QuestionnaireStarsResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'sum_rate' => $this['sum_rate'],
            'count_answers' => $this['count_answers'],
            'avg_rate' => $this['avg_rate'],
        ];
    }
}
