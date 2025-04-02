<?php

declare(strict_types=1);

namespace Honed\Action;

use Honed\Action\Console\Commands\ActionGroupMakeCommand;
use Honed\Action\Console\Commands\ActionMakeCommand;
use Honed\Action\Console\Commands\ActionsMakeCommand;
use Honed\Action\Http\Controllers\ActionController;
use Illuminate\Routing\Router;
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
                ActionGroupMakeCommand::class,
            ]);

            $this->publishes([
                __DIR__.'/../config/action.php' => config_path('action.php'),
            ], 'config');

            $this->publishes([
                __DIR__.'/../stubs' => base_path('stubs'),
            ], 'stubs');
        }

        // $this->registerRoutesMacro();
    }

    /**
     * Register the route macro for the Table class.
     */
    // private function registerRoutesMacro(): void
    // {
    //     Router::macro('action', function () {
    //         $endpoint = type(config('action.endpoint', '/actions'))->asString();

    //         /** @var \Illuminate\Routing\Router $this */
    //         $this->match(
    //             ['post', 'patch', 'put'],
    //             $endpoint,
    //             ActionController::class
    //         )->name('actions');
    //     });
    // }
}
