<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;

beforeEach(function () {
    File::cleanDirectory(app_path('Actions'));
    File::cleanDirectory(app_path('Models'));

    $this->artisan('make:model', ['name' => 'App\\Models\\Product']);

    $this->actions = Config::get('action.model_actions');
});

afterEach(function () {
    File::cleanDirectory(app_path('Actions'));
    File::cleanDirectory(app_path('Models'));
    Config::set('action.model_actions', $this->actions);
});

it('makes actions', function () {
    $this->artisan('make:actions')->assertSuccessful();

    foreach (config('action.model_actions') as $file => $action) {
        $file = app_path("Actions/{$action}.php");

        $this->assertFileExists($file);

        $this->assertStringContainsString(
            "class {$action} extends {$action}Action",
            File::get($file)
        );
    }
});

it('makes with path', function () {
    $this->artisan('make:actions', [
        '--path' => '/Models/',
    ])->assertSuccessful();

    foreach (config('action.model_actions') as $file => $action) {
        $file = app_path("Actions/Models/{$action}.php");

        $this->assertFileExists($file);

        $this->assertStringContainsString(
            "class {$action} extends {$action}Action",
            File::get($file)
        );
    }
});

it('uses default action', function () {
    Config::set('action.model_actions', null);

    $this->artisan('make:actions')->assertSuccessful();

    foreach ($this->actions as $file => $action) {
        $file = app_path("Actions/{$action}.php");

        $this->assertFileExists($file);

        $this->assertStringContainsString(
            "class {$action} extends {$action}Action",
            File::get($file)
        );
    }
});
