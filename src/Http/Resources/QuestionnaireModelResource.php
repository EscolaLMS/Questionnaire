<?php

namespace EscolaLms\Questionnaire\Http\Resources;

use EscolaLms\Questionnaire\Models\QuestionnaireModel;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionnaireModelResource extends JsonResource
{
    public function __construct(QuestionnaireModel $questionnaireModel)
    {
        $this->resource = $questionnaireModel;
    }

    public function toArray($request)
    {
        return [
            'id' => $this->resource->id,
            'model_type_title' => $this->resource->modelableType->title ?? '',
            'model_type_class' => $this->resource->modelableType->model_class ?? '',
            'model_type_id' => $this->resource->model_type_id,
            'model_id' => $this->resource->model_id,
            'model_title' => $this->resource->foreignModel->title ?? $this->resource->foreignModel->name ?? $this->resource->model_id,
        ];
    }
}
