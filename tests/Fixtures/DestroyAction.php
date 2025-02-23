<?php

declare(strict_types=1);

namespace Honed\Action\Tests\Fixtures;

use Honed\Action\Contracts\Actionable;
use Honed\Action\InlineAction;
use Honed\Action\Tests\Stubs\Product;

class DestroyAction extends InlineAction implements Actionable
{
    public function setUp(): void
    {
        parent::setUp();

        $this->name('destroy');
        $this->label(fn (Product $product) => 'Destroy '.$product->name);
    }

    public function handle($product)
    {
        $product->delete();
    }
}
