<?php

declare(strict_types=1);

use Workbench\App\Actions\Product\ForceDestroyProduct;
use Workbench\App\Models\Product;

beforeEach(function () {
    $this->action = new ForceDestroyProduct();
});

it('destroys model', function () {
    $product = Product::factory()->create();

    $this->action->handle($product);

    $this->assertDatabaseMissing('products', [
        'id' => $product->id,
    ]);
});

it('destroys ids', function () {
    $products = Product::factory(2)->create();

    $this->action->handle($products->pluck('id'));

    foreach ($products as $product) {
        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
        ]);
    }
});

it('destroys model collection', function () {
    $products = Product::factory()
        ->count(3)
        ->create();

    $this->action->handle($products);

    $products->each(function ($product) {
        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
        ]);
    });
});
