<?php

declare(strict_types=1);

namespace Honed\Action\Actions;

use Honed\Action\Actions\Concerns\Transactable;

abstract class DatabaseAction extends Action
{
    use Transactable;
}
