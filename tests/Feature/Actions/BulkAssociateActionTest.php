<?php

declare(strict_types=1);

use Workbench\App\Actions\Product\BulkAssociateUser;
use Workbench\App\Models\Product;
use Workbench\App\Models\User;

beforeEach(function () {
    $this->action = new BulkAssociateUser();

    $this->user = User::factory()->create();
});

it('associates an id', function () {
    $product = Product::factory()->create();

    $this->action->handle($product->id, $this->user);

    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'user_id' => $this->user->id,
    ]);
});

it('associates an array of ids', function () {
    $product = Product::factory()->create();

    $this->action->handle([$product->id], $this->user);

    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'user_id' => $this->user->id,
    ]);
});

it('associates a collection of ids', function () {
    $products = Product::factory(3)->create();

    $this->action->handle($products->pluck('id'), $this->user);

    foreach ($products as $product) {
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'user_id' => $this->user->id,
        ]);
    }
});
