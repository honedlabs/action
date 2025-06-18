<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;

beforeEach(function () {
    File::cleanDirectory(app_path('Operations'));
})->skip();

afterEach(function () {
    File::cleanDirectory(app_path('Operations'));
});

it('makes', function () {
    $this->artisan('make:operation', [
        'name' => 'View',
        '--force' => true,
    ])->assertSuccessful();

    $this->assertFileExists(app_path('Operations/View.php'));
});

it('bindings for a name', function () {
    $this->artisan('make:operation', [
        '--force' => true,
    ])->expectsQuestion('What should the operation be named?', 'View')
        ->assertSuccessful();

    $this->assertFileExists(app_path('Operations/View.php'));
});
