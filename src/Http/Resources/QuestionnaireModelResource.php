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
        $model = new $this->modelableType->modelable_class();

        return [
            'id' => $this->id,
            'model_type' => $this->modelableType->modelable_class,
            'model_id' => $this->modelable_id,
            'model' => $model::find($this->modelable_id),
        ];
    }
}
