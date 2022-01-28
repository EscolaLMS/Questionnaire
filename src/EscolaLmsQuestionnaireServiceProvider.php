<?php

namespace EscolaLms\Questionnaire;

use EscolaLms\Core\Providers\Injectable;
use Illuminate\Support\ServiceProvider;

/**
 * SWAGGER_VERSION
 */
class EscolaLmsQuestionnaireServiceProvider extends ServiceProvider
{
    use Injectable;

    private const CONTRACTS = [
        //PageRepositoryContract::class => PageRepository::class,
        //PageServiceContract::class => PageService::class,
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
