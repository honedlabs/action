<?php

declare(strict_types=1);

namespace Honed\Action\Tests\Stubs;

use Honed\Action\ActionGroup;

class ProductActions extends ActionGroup
{
    /**
     * {@inheritdoc}
     */
    public function defineResource()
    {
        return Product::class;
    }

    /**
     * {@inheritdoc}
     */
    public function defineActions()
    {
        return actions();
    }
}
