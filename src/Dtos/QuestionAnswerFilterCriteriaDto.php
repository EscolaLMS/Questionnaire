<?php

namespace EscolaLms\Questionnaire\Dtos;

use EscolaLms\Core\Dtos\Contracts\InstantiateFromRequest;
use EscolaLms\Core\Dtos\CriteriaDto;
use EscolaLms\Core\Repositories\Criteria\Primitives\DateCriterion;
use EscolaLms\Core\Repositories\Criteria\Primitives\EqualCriterion;
use EscolaLms\Questionnaire\Enums\QuestionnairePermissionsEnum;
use EscolaLms\Questionnaire\Repository\Criteria\AuthoredModelsQuestionnaireCriterion;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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

        if ($request->has('updated_at_from')) {
            $criteria->push(
                new DateCriterion(
                    'question_answers.updated_at',
                    Carbon::make($request->get('updated_at_from')),
                    '>='
                )
            );
        }

        if ($request->has('updated_at_to')) {
            $criteria->push(
                new DateCriterion(
                    'question_answers.updated_at',
                    Carbon::make($request->get('updated_at_to')),
                    '<='
                )
            );
        }

        return new self($criteria);
    }
}
