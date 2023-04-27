<?php

namespace EscolaLms\Questionnaire\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QuestionAnswerFrontResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'question_id' => $this->question_id,
            'question_title' => $this->question->title,
            'questionnaire_model_id' => $this->questionnaire_model_id,
            'rate' => $this->rate,
            'note' => $this->note,
            'visible_on_front' => $this->visible_on_front,
            'user' => AnswerUserResource::make($this->user),
        ];
    }
}
