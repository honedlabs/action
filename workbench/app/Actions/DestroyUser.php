<?php

declare(strict_types=1);

namespace Workbench\App\Actions;

use Honed\Action\Contracts\Actionable;
use Workbench\App\Models\User;

class DestroyUser implements Actionable
{
    public function handle(User $user)
    {
        $user->delete();

        return back();
    }
}
