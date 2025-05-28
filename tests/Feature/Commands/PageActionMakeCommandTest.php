<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;

beforeEach(function () {
    File::cleanDirectory(app_path('Actions/Page'));
});

it('makes page action', function () {
    $this->artisan('make:page-action', [
        'name' => 'CreateAction',
    ])->assertSuccessful();

    $this->assertFileExists(app_path('Actions/Page/CreateAction.php'));
});

it('prompts for a page action name', function () {
    $this->artisan('make:page-action')
        ->expectsQuestion('What should the page action be named?', 'CreateAction')
        ->assertSuccessful();

    $this->assertFileExists(app_path('Actions/Page/CreateAction.php'));
});
