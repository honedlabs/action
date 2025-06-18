<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;

beforeEach(function () {
    File::cleanDirectory(app_path('Processes'));
});

it('makes', function () {
    $this->artisan('make:process', [
        'name' => 'UserCreationProcess',
        '--force' => true,
    ])->assertSuccessful();

    $this->assertFileExists(app_path('Processes/UserCreationProcess.php'));
});

it('processs for a name', function () {
    $this->artisan('make:process', [
        '--force' => true,
    ])->expectsQuestion('What should the process be named?', 'UserCreationProcess')
        ->assertSuccessful();

    $this->assertFileExists(app_path('Processes/UserCreationProcess.php'));
});
