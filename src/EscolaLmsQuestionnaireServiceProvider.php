<?php

namespace EscolaLms\Questionnaire;

use EscolaLms\Questionnaire\Repository\Contracts\QuestionAnswerRepositoryContract;
use EscolaLms\Questionnaire\Repository\Contracts\QuestionnaireModelTypeRepositoryContract;
use EscolaLms\Questionnaire\Repository\Contracts\QuestionnaireRepositoryContract;
use EscolaLms\Questionnaire\Repository\Contracts\QuestionRepositoryContract;
use EscolaLms\Questionnaire\Repository\QuestionAnswerRepository;
use EscolaLms\Questionnaire\Repository\QuestionnaireModelTypeRepository;
use EscolaLms\Questionnaire\Repository\QuestionnaireRepository;
use EscolaLms\Questionnaire\Repository\QuestionRepository;
use EscolaLms\Questionnaire\Services\Contracts\QuestionnaireAnswerServiceContract;
use EscolaLms\Questionnaire\Services\QuestionnaireAnswerService;
use Illuminate\Support\ServiceProvider;

/**
 * SWAGGER_VERSION
 */
class EscolaLmsQuestionnaireServiceProvider extends ServiceProvider
{
    public $bindings = [
        QuestionnaireAnswerServiceContract::class => QuestionnaireAnswerService::class,
        QuestionAnswerRepositoryContract::class => QuestionAnswerRepository::class,
        QuestionnaireRepositoryContract::class => QuestionnaireRepository::class,
        QuestionRepositoryContract::class => QuestionRepository::class,
        QuestionnaireModelTypeRepositoryContract::class => QuestionnaireModelTypeRepository::class
    ];

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/routes.php');

        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    public function register()
    {
        parent::register();

        $this->app->register(AuthServiceProvider::class);
    }

    protected function bootForConsole(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }
}
