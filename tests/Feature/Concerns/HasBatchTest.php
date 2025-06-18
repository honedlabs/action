<?php

declare(strict_types=1);

use Workbench\App\Batches\UserBatch;
use Workbench\App\Models\User;

it('has action group', function () {
    expect(User::batch())
        ->toBeInstanceOf(UserBatch::class);
});
