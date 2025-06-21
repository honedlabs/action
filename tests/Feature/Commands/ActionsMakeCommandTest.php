<?php

declare(strict_types=1);

use Honed\Action\Commands\ActionsMakeCommand;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;

beforeEach(function () {
    File::cleanDirectory(app_path('Actions'));
    $this->artisan('make:model', ['name' => 'Product']);
});

afterEach(function () {
    File::cleanDirectory(app_path('Actions'));
});

it('makes actions', function () {
    $this->artisan('make:actions', [
        'model' => 'Product',
    ])->assertSuccessful();

    foreach (config('action.model_actions') as $file => $action) {
        $file = app_path(\sprintf('Actions/Product/%sProduct.php', $action));

        $this->assertFileExists($file);

        $this->assertStringContainsString(
            "class {$action}Product extends {$action}Action",
            File::get($file)
        );
    }
});

it('makes with path', function () {
    $this->artisan('make:actions', [
        'model' => 'Product',
        '--path' => '/Models/',
    ])->assertSuccessful();

    foreach (config('action.model_actions') as $file => $action) {
        $file = app_path(\sprintf('Actions/Models/Product/%sProduct.php', $action));

        $this->assertFileExists($file);

        $this->assertStringContainsString(
            "class {$action}Product extends {$action}Action",
            File::get($file)
        );
    }
});

it('promps for missing model', function () {
    $this->artisan('make:actions')
        ->expectsQuestion('What model should the actions be for?', 'Product')
        ->assertSuccessful();

    foreach (config('action.model_actions') as $file => $action) {
        $file = app_path(\sprintf('Actions/Product/%sProduct.php', $action));

        $this->assertFileExists($file);

        $this->assertStringContainsString(
            "class {$action}Product extends {$action}Action",
            File::get($file)
        );
    }
});

it('errors when model is not provided', function () {
    $this->artisan('make:actions')
        ->expectsQuestion('What model should the actions be for?', 'Stock')
        ->assertFailed();
});

it('errors when model does not exist', function () {
    $this->artisan('make:actions', [
        'model' => 'Stock',
    ])->assertFailed();
});