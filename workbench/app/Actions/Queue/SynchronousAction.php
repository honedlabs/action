<?php

declare(strict_types=1);

namespace Workbench\App\Actions\Queue;

use Honed\Action\Attributes\Synchronous;

#[Synchronous]
class SynchronousAction extends AsynchronousAction
{
    //
}