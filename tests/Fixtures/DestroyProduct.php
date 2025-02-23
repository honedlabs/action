<?php

declare(strict_types=1);

namespace Honed\Action\Tests\Fixtures;

use Honed\Action\Contracts\Actionable;
use Honed\Action\Tests\Stubs\Product;

class DestroyProduct implements Actionable
{
    public function handle(Product $product)
    {
        $product->delete();

        return back();
    }
}
