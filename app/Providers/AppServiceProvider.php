<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Disable migration repository checks
        $this->app->bind('migrator', function ($app) {
            return new \Illuminate\Database\Migrations\Migrator(
                new \App\Database\NullMigrationRepository,
                $app['db'],
                $app['files']
            );
        });
    }
}
