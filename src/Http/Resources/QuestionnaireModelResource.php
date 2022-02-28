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
        //$model = new $this->modelableType->model_class();

        return [
            'id' => $this->id,
            'model_type_title' => $this->modelableType->title,
            'model_type_class' => $this->modelableType->model_class,
            'model_type_id' => $this->model_type_id,
            'model_id' => $this->model_id,
            //'model' => $model::find($this->model_id),
        ];
    }
}
