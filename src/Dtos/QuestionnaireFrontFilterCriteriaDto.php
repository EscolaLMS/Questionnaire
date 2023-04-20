<?php

namespace EscolaLms\Questionnaire\Dtos;

use EscolaLms\Core\Dtos\Contracts\InstantiateFromRequest;
use EscolaLms\Core\Dtos\CriteriaDto;
use EscolaLms\Core\Repositories\Criteria\Primitives\HasCriterion;
use EscolaLms\Questionnaire\Repository\Criteria\ModelQuestionnareCriterion;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class QuestionnaireFrontFilterCriteriaDto extends CriteriaDto implements InstantiateFromRequest
{
    public static function instantiateFromRequest(Request $request): InstantiateFromRequest
    {
        $criteria = new Collection();

        if ($request->has('model_type_title') && $request->has('model_id')) {
            $criteria->push(new ModelQuestionnareCriterion($request->input('model_id'), $request->input('model_type_title')));
        }

        if ($request->has('public_answers')) {
            $criteria->push(
                new HasCriterion(
                    'questions',
                    fn (Builder $q) => $q->where('public_answers', '=', $request->input('public_answers'))
                )
            );
        }

        return new self($criteria);
    }
}
