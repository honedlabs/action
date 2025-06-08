<?php

declare(strict_types=1);

use Honed\Action\Attributes\UseActions;
use Workbench\App\ActionGroups\UserActions;
use Workbench\App\Models\User;

it('has attribute', function () {
    $attribute = new UseActions(UserActions::class);

    expect($attribute)
        ->toBeInstanceOf(UseActions::class)
        ->actionGroupClass->toBe(UserActions::class);

    expect(User::class)
        ->toHaveAttribute(UseActions::class);
});
