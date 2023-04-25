<?php

namespace EscolaLms\Questionnaire\Repository\Criteria;

use EscolaLms\Core\Repositories\Criteria\Criterion;
use Illuminate\Database\Eloquent\Builder;

class AnswerQuestionReviewCriterion extends Criterion
{
    public function apply(Builder $query): Builder
    {
        return $query
            ->whereHas(
                'question',
                fn (Builder $q) => $q
                    ->where(fn (Builder $q) => $q->where('type', '=', $this->value))
            );
    }
}
