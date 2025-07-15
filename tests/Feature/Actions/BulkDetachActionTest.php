<?php

declare(strict_types=1);

use Workbench\App\Actions\Product\BulkDetachUsers;
use Workbench\App\Models\Product;
use Workbench\App\Models\User;

beforeEach(function () {
    $this->action = new BulkDetachUsers();

    $this->users = User::factory(3)->create();
});

it('detaches id', function () {
    $product = Product::factory()
        ->hasAttached($this->users)
        ->create();

    $this->assertDatabaseCount('product_user', 3);

    $this->action->handle($product->getKey(), $this->users);

    $this->assertDatabaseEmpty('product_user');
});

it('detaches array', function () {
    $products = Product::factory(3)
        ->hasAttached($this->users)
        ->create();

    $this->assertDatabaseCount('product_user', 3 * 3);

    $this->action->handle($products->pluck('id')->toArray(), $this->users);

    $this->assertDatabaseEmpty('product_user');
});

it('detaches collection', function () {
    $products = Product::factory(3)
        ->hasAttached($this->users)
        ->create();

    $this->assertDatabaseCount('product_user', 3 * 3);

    $this->action->handle($products, $this->users);

    $this->assertDatabaseEmpty('product_user');
});
