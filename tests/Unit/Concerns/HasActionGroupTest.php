<?php

declare(strict_types=1);

use Honed\Action\Concerns\HasActionGroup;
use Honed\Action\Tests\Stubs\ProductActions;
use Honed\Action\Tests\Stubs\Product;
use Illuminate\Database\Eloquent\Model;

class ActionGroupModel extends Model
{
    use HasActionGroup;

    protected static $actions = ProductActions::class;
}

it('has a cache', function () {
    expect(Product::actions())
        ->toBeInstanceOf(ProductActions::class);
});
