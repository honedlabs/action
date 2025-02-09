<?php

declare(strict_types=1);

namespace Honed\Action\Tests\Stubs;

use Honed\Action\Contracts\HasHandler;
use Honed\Action\InlineAction;

class DestroyAction extends InlineAction implements HasHandler
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
