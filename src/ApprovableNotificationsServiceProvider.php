<?php

namespace ToneflixCode\ApprovableNotifications;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\SplFileInfo;

class ApprovableNotificationsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        /*
         * Optional methods to load your package assets
         */
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/config.php' => config_path('approvable-notifications.php'),
            ], 'approvable-notifications');

            $canMigrate = empty(array_filter(File::files(base_path('database/migrations')), function (SplFileInfo $file) {
                return str($file->getBasename())->contains('create_approvable_notifications_table');
            }));

            if ($canMigrate) {
                $this->publishes([
                    __DIR__ . '/../database/migrations/2024_06_11_172655_create_approvable_notifications_table.php' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_approvable_notifications_table.php'),
                ], 'approvable-notifications');
            }
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'approvable-notifications');

        // Register the main class to use with the facade
        $this->app->singleton('approvable-notifications', function () {
            return new ApprovableNotifications;
        });
    }
}
