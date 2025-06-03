<?php

declare(strict_types=1);

namespace Workbench\App\Actions\Inline;

use Honed\Action\Contracts\Actionable;
use Honed\Action\InlineAction;
use Workbench\App\Models\User;

class DestroyAction extends InlineAction implements Actionable
{
    public function setUp(): void
    {
        parent::setUp();

        $this->name('destroy');
        $this->label(fn (User $user) => 'Destroy '.$user->name);
    }

    public function handle($user)
    {
        $user->delete();
    }
}
