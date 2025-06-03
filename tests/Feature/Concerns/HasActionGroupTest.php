<?php

declare(strict_types=1);

use Workbench\App\ActionGroups\UserActions;
use Workbench\App\Models\User;

it('has action group', function () {
    expect(User::actions())
        ->toBeInstanceOf(UserActions::class);
});
