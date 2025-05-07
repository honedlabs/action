<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;

beforeEach(function () {
    File::cleanDirectory(app_path('Actions/Bulk'));
});

it('makes bulk action', function () {
    $this->artisan('make:bulk-action', [
        'name' => 'Delete',
    ])->assertSuccessful();

    $this->assertFileExists(app_path('Actions/Bulk/Delete.php'));
});

it('prompts for a bulk action name', function () {
    $this->artisan('make:action-group')
        ->expectsQuestion('What should the action group be named?', 'Delete')
        ->assertSuccessful();

    $this->assertFileExists(app_path('Actions/Bulk/Delete.php'));
});
