<?php

namespace EscolaLms\Questionnaire;

use EscolaLms\Questionnaire\Models\Question;
use EscolaLms\Questionnaire\Models\Questionnaire;
use EscolaLms\Questionnaire\Policies\QuestionnairePolicy;
use EscolaLms\Questionnaire\Policies\QuestionPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Questionnaire::class => QuestionnairePolicy::class,
        Question::class => QuestionPolicy::class,
    ];

    public function boot()
    {
        $this->registerPolicies();
    }
}
