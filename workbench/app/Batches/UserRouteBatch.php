<?php

declare(strict_types=1);

namespace Workbench\App\Batches;

class UserRouteBatch extends UserBatch
{
    /**
     * Provide the action group with any necessary setup
     *
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();

        // $this->isNotExecutable();
    }
}
