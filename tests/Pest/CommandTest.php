<?php

declare(strict_types=1);

it('tests', function () {
    $this->artisan('make:action', [
        'name' => 'TestAction',
    ])->assertSuccessful();

    $this->assertFileExists(app_path('Actions/TestAction.php'));
});