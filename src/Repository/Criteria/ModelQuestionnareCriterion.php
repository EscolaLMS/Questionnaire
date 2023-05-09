<?php

namespace EscolaLms\Questionnaire\Repository\Criteria;

use EscolaLms\Core\Repositories\Criteria\Criterion;
use Illuminate\Database\Eloquent\Builder;

class ModelQuestionnareCriterion extends Criterion
{
    private int $modelId;
    private string $modelTitle;
    public function __construct(int $modelId, string $modelTitle)
    {
        parent::__construct();
        $this->modelId = $modelId;
        $this->modelTitle = $modelTitle;
    }

    public function apply(Builder $query): Builder
    {
        return $query
            ->whereHas(
                'questionnaireModels',
                fn (Builder $q) => $q
                    ->whereHas('modelableType', fn (Builder $q) => $q->where('title', '=', $this->modelTitle))
                    ->where('model_id', '=', $this->modelId)
            );
    }
}
