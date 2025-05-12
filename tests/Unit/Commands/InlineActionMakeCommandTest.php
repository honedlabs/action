<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;

beforeEach(function () {
    File::cleanDirectory(app_path('Actions/Inline'));
});

it('makes inline action', function () {
    $this->artisan('make:inline-action', [
        'name' => 'ViewAction',
    ])->assertSuccessful();

    $this->assertFileExists(app_path('Actions/Inline/ViewAction.php'));
});

it('prompts for a inline action name', function () {
    $this->artisan('make:inline-action')
        ->expectsQuestion('What should the inline action be named?', 'ViewAction')
        ->assertSuccessful();

    $this->assertFileExists(app_path('Actions/Inline/ViewAction.php'));
});
