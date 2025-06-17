<?php

declare(strict_types=1);

namespace Honed\Action;

use Honed\Action\Commands\ActionGroupMakeCommand;
use Honed\Action\Commands\ActionMakeCommand;
use Honed\Action\Commands\ActionsMakeCommand;
use Honed\Action\Commands\OperationMakeCommand;
use Honed\Action\Http\Controllers\ActionController;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class ActionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/action.php', 'action');

        // $this->app->bind(ActionGroupHandler::class, ActionHandler::class)

        $this->registerRoutesMacro();
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {

            $this->offerPublishing();

            $this->commands([
                ActionMakeCommand::class,
                ActionsMakeCommand::class,
                ActionGroupMakeCommand::class,
                OperationMakeCommand::class,
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
     * Register the route macro for the action handler.
     *
     * @return void
     */
    private function registerRoutesMacro()
    {
        Router::macro('actions', function () {
            /** @var Router $this */

            $methods = ['post', 'patch', 'put'];

            $endpoint = ActionGroup::getDefaultEndpoint();

            $this->match($methods, $endpoint, [ActionController::class, 'dispatch'])
                ->name('actions');

            $this->match($methods, $endpoint.'/{action}', [ActionController::class, 'invoke'])
                ->name('actions.invoke');
        });
    }
}
