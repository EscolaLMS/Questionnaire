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
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'questionnaire_id' => $this->questionnaire_id,
            'position' => $this->position,
            'active' => $this->active,
        ];
    }
}
