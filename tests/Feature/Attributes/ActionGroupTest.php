<?php

declare(strict_types=1);

use Honed\Action\Attributes\ActionGroup;
use Honed\Action\Tests\Stubs\ProductActions;
use Honed\Action\Tests\Stubs\Product;

it('has attribute', function () {
    $attribute = new ActionGroup(ProductActions::class);
    expect($attribute)
        ->toBeInstanceOf(ActionGroup::class)
        ->actions->toBe(ProductActions::class);

    expect(Product::class)
        ->toHaveAttribute(ActionGroup::class);
});
