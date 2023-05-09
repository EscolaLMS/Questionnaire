<?php

namespace EscolaLms\Questionnaire\Dtos;

use EscolaLms\Core\Dtos\Contracts\InstantiateFromRequest;
use EscolaLms\Core\Dtos\CriteriaDto;
use EscolaLms\Core\Repositories\Criteria\Primitives\HasCriterion;
use EscolaLms\Courses\Repositories\Criteria\Primitives\OrderCriterion;
use EscolaLms\Questionnaire\Repository\Criteria\AnswerQuestionReviewCriterion;
use EscolaLms\Questionnaire\Repository\Criteria\QuestionAnswersCriterion;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class QuestionAnswersCriteriaDto extends CriteriaDto implements InstantiateFromRequest
{
    public static function instantiateFromRequest(Request $request): InstantiateFromRequest
    {
        $criteria = new Collection();

        if ($request->has('question_id') && $request->has('model_id') && $request->has('model_type_title')) {
            $criteria
                ->push(
                    new QuestionAnswersCriterion(
                        $request->input('question_id'),
                        $request->input('model_id'),
                        $request->input('model_type_title'),
                    )
                );
        }

        if ($request->has('type')) {
            $criteria
                ->push(
                    new AnswerQuestionReviewCriterion(null, $request->input('type'))
                );
        }

        if ($request->has('order_by')) {
            $criteria
                ->push(new OrderCriterion($request->input('order_by'), $request->input('order', 'ASC')));
        }

        return new self($criteria);
    }
}
