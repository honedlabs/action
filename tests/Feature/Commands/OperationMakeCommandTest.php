<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;

beforeEach(function () {
    File::cleanDirectory(app_path('Operations'));
});

afterEach(function () {
    File::cleanDirectory(app_path('Operations'));
});

it('makes an inline operation by default', function () {
    $this->artisan('make:operation', [
        'name' => 'View',
        '--force' => true,
    ])->assertSuccessful();

    $file = app_path('Operations/View.php');

    $this->assertFileExists($file);

    $this->assertStringContainsString(
        'class View extends InlineOperation',
        File::get($file)
    );
});

it('bindings for a name', function () {
    $this->artisan('make:operation', [
        '--force' => true,
    ])->expectsQuestion('What should the operation be named?', 'View')
        ->assertSuccessful();

    $file = app_path('Operations/View.php');

    $this->assertFileExists($file);

    $this->assertStringContainsString(
        'class View extends InlineOperation',
        File::get($file)
    );
});

it('makes operation types', function ($flag, $class) {
    $this->artisan('make:operation', [
        'name' => 'View',
        '--force' => true,
        $flag => true,
    ])->assertSuccessful();

    $file = app_path('Operations/View.php');

    $this->assertFileExists($file);

    $this->assertStringContainsString(
        "class View extends {$class}",
        File::get($file)
    );
})->with([
    ['--inline', 'InlineOperation'],
    ['--bulk', 'BulkOperation'],
    ['--page', 'PageOperation'],
]);

it('makes operation with type', function ($type, $class) {
    $this->artisan('make:operation', [
        'name' => 'View',
        '--force' => true,
        '--type' => $type,
    ])->assertSuccessful();

    $file = app_path('Operations/View.php');

    $this->assertFileExists($file);

    $this->assertStringContainsString(
        "class View extends {$class}",
        File::get($file)
    );
})->with([
    ['inline', 'InlineOperation'],
    ['bulk', 'BulkOperation'],
    ['page', 'PageOperation'],
    ['invalid', 'InlineOperation'],
]);
