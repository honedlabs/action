<?php

declare(strict_types=1);

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;

beforeEach(function () {
    File::cleanDirectory(app_path('Actions'));
    File::cleanDirectory(app_path('Models'));

    $this->actions = Config::get('action.actions');
});

afterEach(function () {
    File::cleanDirectory(app_path('Actions'));
    File::cleanDirectory(app_path('Models'));
    Config::set('action.actions', $this->actions);
});

it('makes', function () {
    $this->artisan('make:action', [
        'name' => 'TestAction',
    ])->assertSuccessful();

    $this->assertFileExists(app_path('Actions/TestAction.php'));
});

it('creates actions', function ($name, $action) {
    $this->artisan('make:action', [
        'name' => 'TestAction',
        '--action' => $name,
    ])->assertSuccessful();

    $file = app_path('Actions/TestAction.php');

    $this->assertFileExists($file);

    $this->assertStringContainsString(
        "class TestAction extends {$action}",
        File::get($file)
    );
})->with([
    fn () => Arr::mapWithKeys(config('action.actions'), fn ($value, $key) => [$key, $value]),
]);

it('prompts', function () {
    $this->artisan('make:model', ['name' => 'Product']);

    $this->artisan('make:action')
        ->expectsQuestion('What should the action be named?', 'StoreAction')
        ->expectsQuestion('What action should be used? (Optional)', 'store')
        ->expectsQuestion('What model should this action be for? (Optional)', 'Product')
        ->expectsQuestion('A [App\Models\Product] model does not exist. Do you want to generate it?', true)
        ->assertSuccessful();

    $this->assertFileExists(app_path('Actions/StoreAction.php'));
})->skip(fn () => app()->version() >= '12.0');

it('throws error when invalid action is provided', function () {
    $this->artisan('make:action', [
        'name' => 'TestAction',
        '--action' => 'invalid',
    ]);
})->throws(InvalidArgumentException::class);

it('uses default action', function () {
    Config::set('action.actions', null);

    $this->artisan('make:action', [
        'name' => 'TestAction',
        '--action' => 'store',
    ])->assertSuccessful();

    $file = app_path('Actions/TestAction.php');

    $this->assertFileExists($file);

    $this->assertStringContainsString(
        'class TestAction extends StoreAction',
        File::get($file)
    );
});
