<?php

namespace EscolaLms\Questionnaire\Http\Resources;

use EscolaLms\Questionnaire\Models\QuestionnaireModelType;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionnaireModelTypeResource extends JsonResource
{
    public function __construct(QuestionnaireModelType $questionnaireModelType)
    {
        $this->resource = $questionnaireModelType;
    }

    public function toArray($request)
    {
        return [
            'id' => $this->resource->id,
            'model_class' => $this->resource->model_class,
            'title' => $this->resource->title,
        ];
    }
}
