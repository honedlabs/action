<?php

declare(strict_types=1);

namespace Workbench\App\ActionGroups;

class UserRouteActions extends UserActions
{
    /**
     * Provide the action group with any necessary setup
     *
     * @return void
     */
    public function setUp()
    {
        $this->shouldntExecute();
    }
}
