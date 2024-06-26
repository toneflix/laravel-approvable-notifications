<?php

namespace ToneflixCode\ApprovableNotifications\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\TestCase as Orchestra;
use ToneflixCode\ApprovableNotifications\ApprovableNotificationsServiceProvider;
use ToneflixCode\ApprovableNotifications\Tests\Database\Factories\UserFactory;

abstract class TestCase extends Orchestra
{
    use RefreshDatabase;

    protected $factories = [
        UserFactory::class,
    ];

    public function getEnvironmentSetUp($app)
    {
        loadEnv();

        config()->set('app.key', 'base64:EWcFBKBT8lKlGK8nQhTHY+wg19QlfmbhtO9Qnn3NfcA=');

        config()->set('database.default', 'testing');

        config()->set('app.faker_locale', 'en_NG');

        $migration = include __DIR__ . '/database/migrations/create_schools_tables.php';
        $migration->up();
        $migration2 = include __DIR__ . '/database/migrations/create_users_tables.php';
        $migration2->up();
    }

    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'ToneflixCode\\ApprovableNotifications\\Tests\\Database\\Factories\\' .
                class_basename(
                    $modelName
                ) . 'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            ApprovableNotificationsServiceProvider::class,
        ];
    }
}
