<?php

declare(strict_types=1);

use Workbench\App\Actions\Product\AttachUniqueUsers;
use Workbench\App\Models\Product;
use Workbench\App\Models\User;

beforeEach(function () {
    $this->action = new AttachUniqueUsers();

    $this->product = Product::factory()->create();

    $this->assertDatabaseEmpty('product_user');
});

it('attaches a single model', function () {
    $user = User::factory()->create();

    $this->action->handle($this->product, $user);

    $this->assertDatabaseCount('product_user', 1);

    $this->assertDatabaseHas('product_user', [
        'product_id' => $this->product->id,
        'user_id' => $user->id,
        'is_active' => false,
    ]);
});

it('attaches multiple models', function () {
    $users = User::factory()
        ->count(3)
        ->create();

    $this->action->handle($this->product, $users);

    $this->assertDatabaseCount('product_user', 3);

    $users->each(function ($user) {
        $this->assertDatabaseHas('product_user', [
            'product_id' => $this->product->id,
            'user_id' => $user->id,
            'is_active' => false,
        ]);
    });
});

it('attaches a single key', function () {
    $id = User::factory()
        ->create()
        ->getKey();

    $this->action->handle($this->product, $id);

    $this->assertDatabaseCount('product_user', 1);

    $this->assertDatabaseHas('product_user', [
        'product_id' => $this->product->id,
        'user_id' => $id,
        'is_active' => false,
    ]);
});

it('attaches multiple keys', function () {
    $users = User::factory()
        ->count(3)
        ->create();

    $ids = $users->map(fn ($user) => $user->getKey());

    $this->action->handle($this->product, $ids);

    $this->assertDatabaseCount('product_user', 3);

    $users->each(function ($user) {
        $this->assertDatabaseHas('product_user', [
            'product_id' => $this->product->id,
            'user_id' => $user->id,
            'is_active' => false,
        ]);
    });
});

it('attaches a single model with attributes', function () {
    $this->assertDatabaseEmpty('product_user');

    $user = User::factory()
        ->create();

    $this->action->handle($this->product, $user, ['is_active' => true]);

    $this->assertDatabaseCount('product_user', 1);

    $this->assertDatabaseHas('product_user', [
        'product_id' => $this->product->id,
        'user_id' => $user->id,
        'is_active' => true,
    ]);
});

it('attaches multiple models with attributes', function () {
    $users = User::factory()
        ->count(3)
        ->create();

    $this->action->handle($this->product, $users, ['is_active' => true]);

    $this->assertDatabaseCount('product_user', 3);

    $users->each(function ($user) {
        $this->assertDatabaseHas('product_user', [
            'product_id' => $this->product->id,
            'user_id' => $user->id,
            'is_active' => true,
        ]);
    });
});
