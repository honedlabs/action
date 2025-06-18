<?php

declare(strict_types=1);

use Workbench\App\Actions\Product\ForceDestroyProduct;
use Workbench\App\Models\Product;
use Workbench\App\Models\User;

beforeEach(function () {
    $this->action = new ForceDestroyProduct();

    $this->product = Product::factory()->create();

    $this->assertDatabaseHas('products', [
        'id' => $this->product->id,
    ]);
});

it('force destroys a model', function () {
    $this->action->handle($this->product);

    $this->assertDatabaseMissing('products', [
        'id' => $this->product->id,
    ]);
});

it('force destroys models from builder', function () {
    $products = Product::factory(3)
        ->create();

    $this->action->handle(Product::query()->whereIn('id', $products->pluck('id')));

    $this->assertDatabaseHas('products', [
        'id' => $this->product->id,
    ]);

    $products->each(function ($product) {
        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
        ]);
    });
});

it('force destroys models from relationship', function () {
    $user = User::query()->find(1);

    $products = Product::factory(3)
        ->create();

    $this->action->handle($user->products());

    $this->assertDatabaseMissing('products', [
        'id' => $this->product->id,
    ]);

    $products->each(function ($product) {
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
        ]);
    });
});
