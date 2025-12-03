<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;

beforeEach(function () {
    File::cleanDirectory(app_path('Actions'));
    File::cleanDirectory(app_path('Models'));
});

afterEach(function () {
    File::cleanDirectory(app_path('Actions'));
    File::cleanDirectory(app_path('Models'));
});

it('makes actions', function () {
    $this->artisan('make:action', [
        'name' => 'TestAction',
    ])->assertSuccessful();

    $this->assertFileExists(app_path('Actions/TestAction.php'));
});

it('prompts for an action name', function () {
    $this->artisan('make:action')
        ->expectsQuestion('What should the action be named?', 'TestAction')
        ->assertSuccessful();

    $this->assertFileExists(app_path('Actions/TestAction.php'));
});
