<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;

beforeEach(function () {
    File::cleanDirectory(app_path('Actions/Page'));
});

it('makes page action', function () {
    $this->artisan('make:page-action', [
        'name' => 'Create',
    ])->assertSuccessful();

    $this->assertFileExists(app_path('Actions/Page/Create.php'));
});

it('prompts for a page action name', function () {
    $this->artisan('make:action-group')
        ->expectsQuestion('What should the action group be named?', 'Create')
        ->assertSuccessful();

    $this->assertFileExists(app_path('Actions/Page/Create.php'));
});
