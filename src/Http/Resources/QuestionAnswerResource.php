<?php

namespace EscolaLms\Questionnaire\Http\Resources;

use EscolaLms\Questionnaire\Models\QuestionAnswer;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionAnswerResource extends JsonResource
{
    public function __construct(QuestionAnswer $question)
    {
        $this->resource = $question;
    }

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'question_id' => $this->question_id,
            'question_title' => $this->question->title,
            'questionnaire_model_id' => $this->questionnaire_model_id,
            'rate' => $this->rate,
            'note' => $this->note,
        ];
    }
}
