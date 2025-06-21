<?php

declare(strict_types=1);

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;

use function Pest\Laravel\artisan;

beforeEach(function () {
    File::cleanDirectory(app_path('Actions'));
})->only();

afterEach(function () {
    File::cleanDirectory(app_path('Actions'));
});

it('makes', function () {
    artisan('make:action', [
        'name' => 'TestAction',
    ])->assertSuccessful();

    $this->assertFileExists(app_path('Actions/TestAction.php'));
});

it('creates actions', function ($name, $action) {
    artisan('make:action', [
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
    artisan('make:action')
        ->expectsQuestion('What should the action be named?', 'StoreAction')
        ->expectsQuestion('What action should be used? (Optional)', 'store')
        ->expectsQuestion('What model should this action be for? (Optional)', 'Product')
        ->assertSuccessful();

    $this->assertFileExists(app_path('Actions/StoreAction.php'));
});
