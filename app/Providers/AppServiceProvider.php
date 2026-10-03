<?php

namespace App\Providers;

use Illuminate\Database\Connectors\ConnectionFactory;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // The Turso package replaces the factory for every driver. Restore Laravel
        // for SQLite and other databases, including isolated test fixtures.
        if (config('database.default') !== 'libsql') {
            $this->app->singleton('db.factory', fn ($app) => new ConnectionFactory($app));
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
