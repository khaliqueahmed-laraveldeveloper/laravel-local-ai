<?php

namespace khaliqueahmed\LocalAI;

use Illuminate\Support\ServiceProvider;

class LocalAIServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/local-ai.php', 'local-ai');

        $this->app->singleton('local-ai', function () {
            return new LocalAIEngine();
        });
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/local-ai.php' => config_path('local-ai.php'),
            ], 'config');

            $this->commands([
                Console\InstallCommand::class,
            ]);
        }
    }
}