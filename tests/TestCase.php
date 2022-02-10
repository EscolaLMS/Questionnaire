<?php

namespace EscolaLms\Questionnaire\Tests;

use EscolaLms\Core\Models\User;
use EscolaLms\Questionnaire\EscolaLmsQuestionnaireServiceProvider;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TestCase extends \EscolaLms\Core\Tests\TestCase
{
    use DatabaseTransactions;

    protected function getPackageProviders($app): array
    {
        return [
            ...parent::getPackageProviders($app),
            EscolaLmsQuestionnaireServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('auth.providers.users.model', User::class);
        $app['config']->set('passport.client_uuids', true);
    }

    protected function authenticateAsAdmin(): void
    {
        $this->user = config('auth.providers.users.model')::factory()->create();
        $this->user->guard_name = 'api';
        $this->user->assignRole('admin');
    }
}
