<?php

namespace EscolaLms\Questionnaire\Repository\Criteria;

use EscolaLms\Core\Repositories\Criteria\Criterion;
use Illuminate\Database\Eloquent\Builder;

class QuestionAnswersCriterion extends Criterion
{
    private int $questionId;
    private int $modelId;
    private string $modelTitle;
    public function __construct(int $questionId, int $modelId, string $modelTitle)
    {
        parent::__construct();
        $this->questionId = $questionId;
        $this->modelId = $modelId;
        $this->modelTitle = $modelTitle;
    }

    public function apply(Builder $query): Builder
    {
        return $query
            ->whereHas(
                'question',
                fn (Builder $q) => $q
                    ->where('id', '=', $this->questionId)
            )
            ->whereHas(
                'questionnaireModel',
                fn (Builder $q) => $q
                    ->where('model_id', '=', $this->modelId)
                    ->whereHas(
                        'modelableType',
                        fn (Builder $q) => $q
                            ->where('title', '=', $this->modelTitle)
                    )
            );
    }
}
