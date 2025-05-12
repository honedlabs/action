<?php

declare(strict_types=1);

use Honed\Action\Attributes\Actions;
use Honed\Action\Tests\Stubs\ProductActions;
use Honed\Action\Tests\Stubs\Product;

it('has attribute', function () {
    $attribute = new Actions(ProductActions::class);
    expect($attribute)
        ->toBeInstanceOf(Actions::class)
        ->actions->toBe(ProductActions::class);

    expect(Product::class)
        ->toHaveAttribute(Actions::class);
});
