<?php

namespace EscolaLms\Questionnaire;

use EscolaLms\Core\Providers\Injectable;
use EscolaLms\Questionnaire\Repository\Contracts\QuestionnaireRepositoryContract;
use EscolaLms\Questionnaire\Repository\Contracts\QuestionRepositoryContract;
use EscolaLms\Questionnaire\Repository\QuestionnaireRepository;
use EscolaLms\Questionnaire\Repository\QuestionRepository;
use Illuminate\Support\ServiceProvider;

/**
 * SWAGGER_VERSION
 */
class EscolaLmsQuestionnaireServiceProvider extends ServiceProvider
{
    use Injectable;

    private const CONTRACTS = [
        QuestionnaireRepositoryContract::class => QuestionnaireRepository::class,
        QuestionRepositoryContract::class => QuestionRepository::class,
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
        $this->injectContract(self::CONTRACTS);

        $this->app->register(AuthServiceProvider::class);
    }

    protected function bootForConsole(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }
}
