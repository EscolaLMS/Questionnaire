<?php

namespace EscolaLms\Questionnaire\Http\Resources;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionnaireReportResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'sum_rate' => $this['sum_rate'],
            'count_answers' => $this['count_answers'],
            'avg_rate' => $this['avg_rate'],
            'question_id' => $this['question_id'],
            'title' => $this['title'],
        ];
    }
}
