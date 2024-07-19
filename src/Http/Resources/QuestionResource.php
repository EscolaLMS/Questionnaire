<?php

namespace EscolaLms\Questionnaire\Http\Resources;

use EscolaLms\Questionnaire\Models\Question;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionResource extends JsonResource
{
    public function __construct(Question $question)
    {
        $this->resource = $question;
    }

    public function toArray($request)
    {
        return [
            'id' => $this->resource->id,
            'title' => $this->resource->title,
            'description' => $this->resource->description,
            'questionnaire_id' => $this->resource->questionnaire_id,
            'position' => $this->resource->position,
            'active' => $this->resource->active,
            'type' => $this->resource->type,
            'public_answers' => $this->resource->public_answers,
        ];
    }
}
