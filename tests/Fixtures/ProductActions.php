<?php

declare(strict_types=1);

namespace Honed\Action\Tests\Fixtures;

use Honed\Action\ActionGroup;
use Honed\Action\Tests\Stubs\Product;

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
