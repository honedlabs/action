<?php

declare(strict_types=1);

namespace Honed\Action\Tests\Stubs;

class RouteProductActions extends ProductActions
{
    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->executes(false);
    }
}
