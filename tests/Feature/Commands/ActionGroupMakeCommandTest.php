<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;

beforeEach(function () {
    File::cleanDirectory(app_path('Actions/Groups'));
});

it('makes action group', function () {
    $this->artisan('make:action-group', [
        'name' => 'UserActions',
    ])->assertSuccessful();

    $this->assertFileExists(app_path('Actions/Groups/UserActions.php'));
});

it('prompts for a action group name', function () {
    $this->artisan('make:action-group')
        ->expectsQuestion('What should the action group be named?', 'UserActions')
        ->assertSuccessful();

    $this->assertFileExists(app_path('Actions/Groups/UserActions.php'));
});
