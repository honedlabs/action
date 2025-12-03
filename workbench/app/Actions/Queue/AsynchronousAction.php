<?php

declare(strict_types=1);

namespace Workbench\App\Actions\Queue;

use Honed\Action\Actions\Action;
use Honed\Action\Concerns\Queueable;
use Honed\Action\Contracts\Queueable as QueueableContract;
use Workbench\App\Models\User;

class AsynchronousAction extends Action implements QueueableContract
{
    use Queueable;

    /**
     * Handle the action.
     */
    public function handle(): User
    {
        return User::factory()->create();
    }
}
