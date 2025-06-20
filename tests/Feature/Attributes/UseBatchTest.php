<?php

declare(strict_types=1);

use Honed\Action\Attributes\UseBatch;
use Workbench\App\Batches\UserBatch;
use Workbench\App\Models\User;

it('has attribute', function () {
    $attribute = new UseBatch(UserBatch::class);

    expect($attribute)
        ->toBeInstanceOf(UseBatch::class)
        ->batchClass->toBe(UserBatch::class);

    expect(User::class)
        ->toHaveAttribute(UseBatch::class);
});
