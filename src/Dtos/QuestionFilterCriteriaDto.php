<?php

namespace EscolaLms\Questionnaire\Dtos;

use EscolaLms\Core\Dtos\Contracts\InstantiateFromRequest;
use EscolaLms\Core\Dtos\CriteriaDto;
use EscolaLms\Core\Repositories\Criteria\Primitives\EqualCriterion;
use EscolaLms\Core\Repositories\Criteria\Primitives\LikeCriterion;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class QuestionFilterCriteriaDto extends CriteriaDto implements InstantiateFromRequest
{
    public static function instantiateFromRequest(Request $request): self
    {
        $criteria = new Collection();

        if ($request->has('title')) {
            $criteria->push(new LikeCriterion('title', $request->input('title')));
        }

        if ($request->has('questionnaire_id')) {
            $criteria->push(new EqualCriterion('questionnaire_id', $request->input('questionnaire_id')));
        }

        return new self($criteria);
    }
}
