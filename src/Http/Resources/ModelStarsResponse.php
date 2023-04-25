<?php

namespace EscolaLms\Questionnaire\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

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
