<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;

beforeEach(function () {
    File::cleanDirectory(app_path('Actions/Inline'));
});

it('makes inline action', function () {
    $this->artisan('make:inline-action', [
        'name' => 'View',
    ])->assertSuccessful();

    $this->assertFileExists(app_path('Actions/Inline/View.php'));
});

it('prompts for a inline action name', function () {
    $this->artisan('make:action-group')
        ->expectsQuestion('What should the action group be named?', 'View')
        ->assertSuccessful();

    $this->assertFileExists(app_path('Actions/Inline/View.php'));
});
