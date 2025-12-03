<?php

declare(strict_types=1);

use Honed\Action\Exceptions\CannotQueueSynchronousActionException;
use Workbench\App\Actions\Queue\SynchronousAction;

it('throws', function () {
    CannotQueueSynchronousActionException::throw(SynchronousAction::class);
})->throws(CannotQueueSynchronousActionException::class);
