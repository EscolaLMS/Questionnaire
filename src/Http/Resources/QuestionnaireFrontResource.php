<?php

namespace EscolaLms\Questionnaire\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QuestionnaireFrontResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this['id'],
            'title' => $this['title'],
            'questions' => QuestionFrontCollection::make($this['questions']),
        ];
    }
}
