<?php

declare(strict_types=1);

use Workbench\App\Actions\Product\BulkDissociateUser;
use Workbench\App\Models\Product;
use Workbench\App\Models\User;

beforeEach(function () {
    $this->action = new BulkDissociateUser();

    $this->user = User::factory()->create();
});

it('dissociates id', function () {
    $product = Product::factory()
        ->for($this->user)
        ->create();

    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'user_id' => $this->user->id,
    ]);

    $this->action->handle($product->getKey());

    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'user_id' => null,
    ]);
});

it('dissociates array', function () {
    $products = Product::factory(3)
        ->for($this->user)
        ->create();

    $this->assertDatabaseCount('products', 3);

    $this->action->handle($products->pluck('id')->toArray());

    foreach ($products as $product) {
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'user_id' => null,
        ]);
    }
});

it('dissociates collection', function () {
    $products = Product::factory(3)
        ->for($this->user)
        ->create();

    $this->assertDatabaseCount('products', 3);

    $this->action->handle($products);

    foreach ($products as $product) {
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'user_id' => null,
        ]);
    }
});
