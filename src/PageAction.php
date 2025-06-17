<?php

declare(strict_types=1);

namespace Honed\Action;

use Honed\Action\Concerns\HandlesBulkActions;

class PageAction extends Action
{
    use HandlesBulkActions;

    /**
     * Provide the instance with any necessary setup.
     *
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();

        $this->type(Action::PAGE);
    }
}
