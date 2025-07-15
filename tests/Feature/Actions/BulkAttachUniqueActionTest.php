<?php

declare(strict_types=1);

use Workbench\App\Actions\Product\BulkAttachUniqueUsers;
use Workbench\App\Models\Product;
use Workbench\App\Models\User;

beforeEach(function () {
    $this->action = new BulkAttachUniqueUsers();

    $this->users = User::factory(3)->create();

    $this->product = Product::factory()->create();

    $this->product->users()->attach($this->users);

    foreach ($this->users as $user) {
        $this->assertDatabaseHas('product_user', [
            'product_id' => $this->product->id,
            'user_id' => $user->id,
        ]);
    }
});

it('attaches id', function () {
    $user = User::factory()->create();

    $this->users->push($user);

    $this->action->handle($this->product->id, $this->users);

    $this->assertDatabaseCount('product_user', 4);

    foreach ($this->users as $user) {
        $this->assertDatabaseHas('product_user', [
            'product_id' => $this->product->id,
            'user_id' => $user->id,
        ]);
    }
});

it('attaches array', function () {
    $products = Product::factory(3)->create();

    $products->push($this->product);

    $this->action->handle($products->modelKeys(), $this->users);

    $this->assertDatabaseCount('product_user', 3 + 3 * 3);

    foreach ($this->users as $user) {
        $this->assertDatabaseHas('product_user', [
            'product_id' => $this->product->id,
            'user_id' => $user->id,
        ]);
    }
});

it('attaches collection', function () {
    $products = Product::factory(3)->create();

    $products->push($this->product);

    $this->action->handle($products, $this->users);

    $this->assertDatabaseCount('product_user', 3 + 3 * 3);

    foreach ($this->users as $user) {
        foreach ($products as $product) {
            $this->assertDatabaseHas('product_user', [
                'product_id' => $product->id,
                'user_id' => $user->id,
            ]);
        }
    }
});
