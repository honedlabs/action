<?php

declare(strict_types=1);

use Honed\Action\Attributes\ActionGroup;
use Workbench\App\ActionGroups\UserActions;
use Workbench\App\Models\User;

it('has attribute', function () {
    $attribute = new ActionGroup(UserActions::class);
    expect($attribute)
        ->toBeInstanceOf(ActionGroup::class)
        ->getGroup()->toBe(UserActions::class);

    expect(User::class)
        ->toHaveAttribute(ActionGroup::class);
});
