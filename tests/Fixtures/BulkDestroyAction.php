<?php

declare(strict_types=1);

namespace Honed\Action\Tests\Fixtures;

use Honed\Action\BulkAction;
use Honed\Action\Contracts\Actionable;
use Honed\Action\Contracts\ShouldChunk;
use Honed\Action\Tests\Stubs\Product;

class BulkDestroyAction extends BulkAction implements Actionable, ShouldChunk
{
    public function setUp(): void
    {
        $this->name('destroy');
        $this->label(fn (Product $product) => 'Destroy '.$product->name);
    }

    public function handle($product)
    {
        $product->delete();
    }
}
