<?php

namespace EscolaLms\Questionnaire\Http\Resources;

use EscolaLms\Questionnaire\Models\Questionnaire;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionnaireResource extends JsonResource
{
    public function __construct(Questionnaire $questionnaire)
    {
        $this->resource = $questionnaire;
    }

    public function toArray($request)
    {
        return [
            'id' => $this->resource->id,
            'title' => $this->resource->title,
            'active' => $this->resource->active,
            'questions' => QuestionResource::collection($this->resource->questions),
            'models' => QuestionnaireModelResource::collection($this->resource->questionnaireModels),
        ];
    }
}
