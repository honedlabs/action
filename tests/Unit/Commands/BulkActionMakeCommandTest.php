<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;

beforeEach(function () {
    File::cleanDirectory(app_path('Actions/Bulk'));
});

it('makes bulk action', function () {
    $this->artisan('make:bulk-action', [
        'name' => 'DeleteAction',
    ])->assertSuccessful();

    $this->assertFileExists(app_path('Actions/Bulk/DeleteAction.php'));
});

it('prompts for a bulk action name', function () {
    $this->artisan('make:bulk-action')
        ->expectsQuestion('What should the bulk action be named?', 'DeleteAction')
        ->assertSuccessful();

    $this->assertFileExists(app_path('Actions/Bulk/DeleteAction.php'));
});
