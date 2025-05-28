<?php

declare(strict_types=1);

namespace Honed\Action\Tests;

use Honed\Action\ActionServiceProvider;
use Honed\Action\Tests\Stubs\Status;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Inertia\Inertia;
use Inertia\ServiceProvider as InertiaServiceProvider;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    use RefreshDatabase;
    use WithWorkbench;

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        View::addLocation(__DIR__.'/Stubs');
        Inertia::setRootView('app');

        config()->set('inertia.testing.ensure_pages_exist', false);
        config()->set('inertia.testing.page_paths', [realpath(__DIR__)]);
    }

    /**
     * Define the database migrations.
     *
     * @return void
     */
    protected function defineDatabaseMigrations()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('status')->default(Status::Available->value);
            $table->unsignedInteger('price')->default(0);
            $table->boolean('best_seller')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Define the routes setup.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    protected function defineRoutes($router)
    {
        $router->middleware(SubstituteBindings::class)
            ->group(function ($router) {
                $router->get('/', fn () => Inertia::render('Home'))->name('home.index');
                $router->get('/products', fn () => Inertia::render('Products/Index'))->name('products.index');
                $router->get('/products/{product}', fn () => Inertia::render('Products/Show'))->name('products.show');
                $router->get('/products/create', fn () => Inertia::render('Products/Create'))->name('products.create');
                $router->actions();
            }
            );
    }

    /**
     * Define the environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    public function getEnvironmentSetUp($app)
    {
        config()->set('action', require __DIR__.'/../config/action.php');
        config()->set('database.default', 'testing');
        config()->set('app.key', 'base64:'.base64_encode(random_bytes(32)));
    }
}
