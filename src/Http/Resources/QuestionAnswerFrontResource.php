<?php

namespace EscolaLms\Questionnaire\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QuestionAnswerFrontResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->resource->id,
            'question_id' => $this->resource->question_id,
            'question_title' => $this->resource->question->title,
            'questionnaire_model_id' => $this->resource->questionnaire_model_id,
            'rate' => $this->resource->rate,
            'note' => $this->resource->note,
            'visible_on_front' => $this->resource->visible_on_front,
            'user' => AnswerUserFrontResource::make($this->resource->user),
            'created_at' => $this->resource->create_at,
            'updated_at' => $this->resource->updated_at,
        ];
    }
}
