<?php

namespace EscolaLms\Questionnaire\Dtos;

use EscolaLms\Core\Dtos\Contracts\InstantiateFromRequest;
use EscolaLms\Core\Dtos\CriteriaDto;
use EscolaLms\Core\Repositories\Criteria\Primitives\LikeCriterion;
use EscolaLms\Questionnaire\Enums\QuestionnairePermissionsEnum;
use EscolaLms\Questionnaire\Repository\Criteria\AuthoredModelsQuestionnaireCriterion;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class QuestionnairesFilterCriteriaDto extends CriteriaDto implements InstantiateFromRequest
{
    public static function instantiateFromRequest(Request $request): self
    {
        $criteria = new Collection();

        if ($request->has('title')) {
            $criteria->push(new LikeCriterion('title', $request->input('title')));
        }

        if (Auth::user() && Auth::user()->can(QuestionnairePermissionsEnum::QUESTIONNAIRE_LIST_AUTHORED)) {
            $criteria->push(new AuthoredModelsQuestionnaireCriterion());
        }

        return new self($criteria);
    }
}
