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
            'id' => $this->id,
            'title' => $this->title,
            /*'model' => $this->model,
            'model_id' => $this->model_id,*/
            'active' => $this->active,
        ];
    }
}
