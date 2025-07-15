<?php

declare(strict_types=1);

use Workbench\App\Actions\Product\ForceDeleteProduct;
use Workbench\App\Models\Product;
use Workbench\App\Models\User;

beforeEach(function () {
    $this->action = new ForceDeleteProduct();
});

it('deletes model', function () {
    $product = Product::factory()->create();

    $this->action->handle($product);

    $this->assertDatabaseMissing('products', [
        'id' => $product->id,
    ]);
});

it('deletes collection', function () {
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

it('deletes builder', function () {
    $products = Product::factory()
        ->count(3)
        ->create();

    $this->action->handle(Product::query()->whereIn('id', $products->pluck('id')));

    $products->each(function ($product) {
        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
        ]);
    });
});

it('deletes relationship', function () {
    $user = User::factory()->create();

    $products = Product::factory()
        ->hasAttached($user)
        ->count(3)
        ->create();

    $this->action->handle($user->products());

    $products->each(function ($product) {
        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
        ]);
    });
})->todo();
