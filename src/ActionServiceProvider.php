<?php

declare(strict_types=1);

namespace Honed\Action;

use Honed\Action\Commands\ActionGroupMakeCommand;
use Honed\Action\Commands\ActionMakeCommand;
use Honed\Action\Commands\ActionsMakeCommand;
use Honed\Action\Commands\BulkActionMakeCommand;
use Honed\Action\Commands\InlineActionMakeCommand;
use Honed\Action\Commands\PageActionMakeCommand;
use Honed\Action\Http\Controllers\ActionController;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class ActionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/action.php', 'action');

        $this->registerRoutesMacro();
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {

        if ($this->app->runningInConsole()) {

            $this->offerPublishing();

            $this->commands([
                ActionMakeCommand::class,
                ActionsMakeCommand::class,
                ActionGroupMakeCommand::class,
                InlineActionMakeCommand::class,
                BulkActionMakeCommand::class,
                PageActionMakeCommand::class,
            ]);
        }
    }

    /**
     * Register the publishing for the package.
     *
     * @return void
     */
    protected function offerPublishing()
    {
        $this->publishes([
            __DIR__.'/../config/action.php' => config_path('action.php'),
        ], 'action-config');

        $this->publishes([
            __DIR__.'/../stubs' => base_path('stubs'),
        ], 'action-stubs');
    }

    /**
     * Register the route macro for the Table class.
     */
    private function registerRoutesMacro(): void
    {
        Router::macro('actions', function () {
            /** @var Router $this */
            $endpoint = type(config('action.endpoint', '/actions'))->asString();

            $methods = ['post', 'patch', 'put'];

            $this->match($methods, $endpoint, [ActionController::class, 'dispatch'])
                ->name('actions');

            $this->match($methods, $endpoint.'/{action}', [ActionController::class, 'invoke'])
                ->name('actions.invoke');
        });
    }
}
