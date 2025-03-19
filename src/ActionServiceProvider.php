<?php

declare(strict_types=1);

namespace Honed\Action;

use Honed\Action\Console\Commands\ActionMakeCommand;
use Honed\Action\Console\Commands\ActionsMakeCommand;
use Illuminate\Support\ServiceProvider;

class ActionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/action.php', 'action');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                ActionMakeCommand::class,
                ActionsMakeCommand::class,
            ]);

            $this->publishes([
                __DIR__.'/../config/action.php' => config_path('action.php'),
            ], 'config');

            $this->publishes([
                __DIR__.'/../stubs' => base_path('stubs'),
            ], 'stubs');
        }
    }
}
