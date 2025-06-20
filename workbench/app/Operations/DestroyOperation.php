<?php

declare(strict_types=1);

namespace Workbench\App\Operations;

use Honed\Action\Actions\DestroyAction;
use Honed\Action\Operations\InlineOperation;

class DestroyOperation extends InlineOperation
{
    /**
     * Provide the instance with any necessary setup.
     *
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();

        $this->name('destroy');
        $this->label(fn ($record) => 'Destroy '.$record->name);
        $this->action(DestroyAction::class);
    }
}
