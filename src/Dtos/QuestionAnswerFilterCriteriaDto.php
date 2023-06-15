<?php

namespace EscolaLms\Questionnaire\Dtos;

use EscolaLms\Core\Dtos\Contracts\InstantiateFromRequest;
use EscolaLms\Core\Dtos\CriteriaDto;
use EscolaLms\Core\Repositories\Criteria\Primitives\EqualCriterion;
use EscolaLms\Core\Repositories\Criteria\Primitives\LikeCriterion;
use EscolaLms\Questionnaire\Enums\QuestionnairePermissionsEnum;
use EscolaLms\Questionnaire\Repository\Criteria\AuthoredModelsQuestionnaireCriterion;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class QuestionAnswerFilterCriteriaDto extends CriteriaDto implements InstantiateFromRequest
{
    public static function instantiateFromRequest(Request $request): self
    {
        $criteria = new Collection();

        if ($request->has('user_id')) {
            $criteria->push(new EqualCriterion('user_id', $request->input('user_id')));
        }

        if ($request->has('questionnaire_model_id')) {
            $criteria->push(new EqualCriterion('questionnaire_model_id', $request->input('questionnaire_model_id')));
        }

        if (Auth::user() && Auth::user()->can(QuestionnairePermissionsEnum::QUESTIONNAIRE_LIST_AUTHORED)) {
            $criteria->push(new AuthoredModelsQuestionnaireCriterion('questionnaireModel'));
        }

        return new self($criteria);
    }
}
