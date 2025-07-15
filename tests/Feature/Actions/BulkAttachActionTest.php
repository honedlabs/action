<?php

declare(strict_types=1);

use Workbench\App\Actions\Product\BulkAttachUsers;
use Workbench\App\Models\Product;
use Workbench\App\Models\User;

beforeEach(function () {
    $this->action = new BulkAttachUsers();

    $this->users = User::factory(3)->create();

    $this->assertDatabaseEmpty('product_user');
});

it('attaches id', function () {
    $product = Product::factory()->create();

    $this->action->handle($product->id, $this->users);

    foreach ($this->users as $user) {
        $this->assertDatabaseHas('product_user', [
            'product_id' => $product->id,
            'user_id' => $user->id,
        ]);
    }
});

it('attaches array', function () {
    $products = Product::factory(3)->create();

    $this->action->handle($products->pluck('id')->all(), $this->users);

    foreach ($this->users as $user) {
        foreach ($products as $product) {
            $this->assertDatabaseHas('product_user', [
                'product_id' => $product->id,
                'user_id' => $user->id,
            ]);
        }
    }
});

it('attaches collection', function () {
    $products = Product::factory(3)->create();

    $this->action->handle($products->modelKeys(), $this->users);

    foreach ($this->users as $user) {
        foreach ($products as $product) {
            $this->assertDatabaseHas('product_user', [
                'product_id' => $product->id,
                'user_id' => $user->id,
            ]);
        }
    }
});
