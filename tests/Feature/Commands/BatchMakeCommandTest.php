<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;

beforeEach(function () {
    File::cleanDirectory(app_path('Batches'));
});

it('makes batch', function () {
    $this->artisan('make:batch', [
        'name' => 'UserActions',
    ])->assertSuccessful();

    $this->assertFileExists(app_path('Batches/UserActions.php'));
});

it('prompts for a batch name', function () {
    $this->artisan('make:batch')
        ->expectsQuestion('What should the batch be named?', 'UserActions')
        ->assertSuccessful();

    $this->assertFileExists(app_path('Batches/UserActions.php'));
});
