<?php

namespace EscolaLms\Questionnaire\Repository\Criteria;

use EscolaLms\Consultations\Models\Consultation;
use EscolaLms\Core\Repositories\Criteria\Criterion;
use EscolaLms\Courses\Models\Course;
use EscolaLms\Webinar\Models\Webinar;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\Auth;

class AuthoredModelsQuestionnaireCriterion extends Criterion
{
    public function __construct(?string $key = 'questionnaireModels')
    {
        parent::__construct($key);
    }

    public function apply(Builder $query): Builder
    {
        $user = Auth::user();
        return $query->where(function (Builder $query) use ($user) {
            if (class_exists(Course::class)) {
                $query->where(function (Builder $query) use ($user) {
                    return $query->whereHas($this->key, function (Builder $q) use ($user) {
                        $q->whereHas('modelableType', fn(Builder $q) => $q
                            ->where('model_class', '=', Course::class))
                            ->join('courses', function (JoinClause $join) {
                                $join->on('courses.id', '=', 'questionnaire_models.model_id');
                            })
                            ->join('course_author', function (JoinClause $join) use ($user) {
                                $join->on('course_author.course_id', '=', 'courses.id')
                                    ->where('course_author.author_id', '=', $user->getKey());
                            });
                    });
                });
            }
            if (class_exists(Consultation::class)) {
                $query->orWhere(function (Builder $query) use ($user) {
                    return $query->whereHas($this->key, function (Builder $q) use ($user) {
                        $q->whereHas('modelableType', fn(Builder $q) => $q
                            ->where('model_class', '=', Consultation::class))
                            ->join('consultations', function (JoinClause $join) use ($user) {
                                $join->on('consultations.id', '=', 'questionnaire_models.model_id')
                                    ->where('consultations.author_id', '=', $user->getKey());
                            });
                    });
                });
            }
            if (class_exists(Webinar::class)) {
                $query->orWhere(function (Builder $query) use ($user) {
                    return $query->whereHas($this->key, function (Builder $q) use ($user) {
                        $q->whereHas('modelableType', fn(Builder $q) => $q
                            ->where('model_class', '=', Webinar::class))
                            ->join('webinars', function (JoinClause $join) {
                                $join->on('webinars.id', '=', 'questionnaire_models.model_id');
                            })
                            ->join('webinar_trainers', function (JoinClause $join) use ($user) {
                                $join->on('webinar_trainers.webinar_id', '=', 'webinars.id')
                                    ->where('webinar_trainers.trainer_id', '=', $user->getKey());
                            });
                    });
                });
            }
        });
    }
}
