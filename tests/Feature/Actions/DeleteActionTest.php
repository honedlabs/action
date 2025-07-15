<?php

declare(strict_types=1);

use Workbench\App\Actions\Product\DeleteProduct;
use Workbench\App\Models\Product;
use Workbench\App\Models\User;

beforeEach(function () {
    $this->action = new DeleteProduct();
});

it('destroys a model', function () {
    $product = Product::factory()->create();

    $this->action->handle($product);

    $this->assertSoftDeleted('products', [
        'id' => $product->id,
    ]);
});

it('destroys multiple models from collection', function () {
    $products = Product::factory()
        ->count(3)
        ->create();

    $this->action->handle($products);

    $products->each(function ($product) {
        $this->assertSoftDeleted('products', [
            'id' => $product->id,
        ]);
    });
});

it('destroy models from builder', function () {
    $products = Product::factory()
        ->count(3)
        ->create();

    $this->action->handle(Product::query()->whereIn('id', $products->pluck('id')));

    $products->each(function ($product) {
        $this->assertSoftDeleted('products', [
            'id' => $product->id,
        ]);
    });
});

it('destroys models from relationship', function () {
    $user = User::factory()->create();

    $products = Product::factory()
        ->hasAttached($user)
        ->count(3)
        ->create();

    $this->action->handle($user->products());

    $products->each(function ($product) {
        $this->assertNotSoftDeleted('products', [
            'id' => $product->id,
        ]);
    });
});
