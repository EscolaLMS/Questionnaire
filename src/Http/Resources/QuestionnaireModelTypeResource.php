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
            'id' => $this->id,
            'model_class' => $this->model_class,
            'title' => $this->title,
        ];
    }
}
