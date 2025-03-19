<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;

beforeEach(function () {
    File::cleanDirectory(app_path('Actions'));
});

it('makes', function () {
    $this->artisan('make:action', [
        'name' => 'TestAction',
    ])->assertSuccessful();

    $this->assertFileExists(app_path('Actions/TestAction.php'));
});

it('sets verb', function () {
    $this->artisan('make:action', [
        'name' => 'UpdateAction',
        '--action' => 'update',
        '--model' => 'Product',
    ])->assertSuccessful();

    $this->assertFileExists(app_path('Actions/UpdateAction.php'));
});

it('prompts', function () {
    $this->artisan('make:action')
        ->expectsQuestion('What should the action be named?', 'StoreAction')
        ->expectsQuestion('What action should be used? (Optional)', 'store')
        ->expectsQuestion('What model should this action be for? (Optional)', 'Product')
        ->assertSuccessful();

    $this->assertFileExists(app_path('Actions/StoreAction.php'));
});
