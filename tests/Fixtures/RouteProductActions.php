<?php

declare(strict_types=1);

namespace Honed\Action\Tests\Fixtures;

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
