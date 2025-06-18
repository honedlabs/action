<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;

beforeEach(function () {
    File::cleanDirectory(app_path('Assemblers'));
});

it('makes', function () {
    $this->artisan('make:assembler', [
        'name' => 'CreateAssembler',
        '--force' => true,
    ])->assertSuccessful();

    $this->assertFileExists(app_path('Assemblers/CreateAssembler.php'));
});

it('bindings for a name', function () {
    $this->artisan('make:assembler', [
        '--force' => true,
    ])->expectsQuestion('What should the assembler be named?', 'CreateAssembler')
        ->assertSuccessful();

    $this->assertFileExists(app_path('Assemblers/CreateAssembler.php'));
});
