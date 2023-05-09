<?php

namespace EscolaLms\Questionnaire\Http\Resources;

use EscolaLms\Core\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class AnswerUserResource extends JsonResource
{
    public function getUser(): User
    {
        return $this->resource;
    }

    public function toArray($request): array
    {
        return [
            'id' => $this->getUser()->getKey(),
            'name' => $this->getUser()->name,
            'avatar' => $this->getUser()->avatar_url,
        ];
    }
}
