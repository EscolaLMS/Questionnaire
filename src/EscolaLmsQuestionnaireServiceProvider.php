<?php

namespace EscolaLms\Questionnaire;

use EscolaLms\Questionnaire\Repository\Contracts\QuestionAnswerRepositoryContract;
use EscolaLms\Questionnaire\Repository\Contracts\QuestionnaireModelRepositoryContract;
use EscolaLms\Questionnaire\Repository\Contracts\QuestionnaireModelTypeRepositoryContract;
use EscolaLms\Questionnaire\Repository\Contracts\QuestionnaireRepositoryContract;
use EscolaLms\Questionnaire\Repository\Contracts\QuestionRepositoryContract;
use EscolaLms\Questionnaire\Repository\QuestionAnswerRepository;
use EscolaLms\Questionnaire\Repository\QuestionnaireModelRepository;
use EscolaLms\Questionnaire\Repository\QuestionnaireModelTypeRepository;
use EscolaLms\Questionnaire\Repository\QuestionnaireRepository;
use EscolaLms\Questionnaire\Repository\QuestionRepository;
use EscolaLms\Questionnaire\Services\Contracts\QuestionnaireAnswerServiceContract;
use EscolaLms\Questionnaire\Services\Contracts\QuestionnaireModelServiceContract;
use EscolaLms\Questionnaire\Services\Contracts\QuestionnaireServiceContract;
use EscolaLms\Questionnaire\Services\Contracts\QuestionServiceContract;
use EscolaLms\Questionnaire\Services\QuestionnaireAnswerService;
use EscolaLms\Questionnaire\Services\QuestionnaireModelService;
use EscolaLms\Questionnaire\Services\QuestionnaireService;
use EscolaLms\Questionnaire\Services\QuestionService;
use Illuminate\Support\ServiceProvider;

/**
 * SWAGGER_VERSION
 */
class EscolaLmsQuestionnaireServiceProvider extends ServiceProvider
{
    public $bindings = [
        QuestionnaireAnswerServiceContract::class => QuestionnaireAnswerService::class,
        QuestionnaireModelServiceContract::class => QuestionnaireModelService::class,
        QuestionnaireServiceContract::class => QuestionnaireService::class,
        QuestionServiceContract::class => QuestionService::class,
        QuestionAnswerRepositoryContract::class => QuestionAnswerRepository::class,
        QuestionnaireRepositoryContract::class => QuestionnaireRepository::class,
        QuestionRepositoryContract::class => QuestionRepository::class,
        QuestionnaireModelTypeRepositoryContract::class => QuestionnaireModelTypeRepository::class,
        QuestionnaireModelRepositoryContract::class => QuestionnaireModelRepository::class,
    ];

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/routes.php');
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'questionnaire');

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
