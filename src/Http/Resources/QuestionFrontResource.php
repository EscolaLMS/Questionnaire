<?php

namespace EscolaLms\Questionnaire\Http\Resources;

use EscolaLms\Questionnaire\Models\Question;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionFrontResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this['id'],
            'title' => $this['title'],
            'description' => $this['description'],
            'rate' => $this['rate'],
        ];
    }
}
