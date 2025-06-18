<?php

declare(strict_types=1);

use Workbench\App\Actions\Product\SyncUsers;
use Workbench\App\Models\Product;
use Workbench\App\Models\User;

beforeEach(function () {
    $this->action = new SyncUsers();

    $this->product = Product::factory()->create();

    $this->assertDatabaseEmpty('product_user');
});

it('syncs a single model', function () {
    $user = User::factory()->create();

    $this->action->handle($this->product, $user);

    $this->assertDatabaseCount('product_user', 1);

    $this->assertDatabaseHas('product_user', [
        'product_id' => $this->product->id,
        'user_id' => $user->id,
        'is_active' => false,
    ]);
});

it('syncs multiple models', function () {
    $users = User::factory(3)->create();

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

it('syncs a single key', function () {
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

it('syncs multiple keys', function () {
    $users = User::factory(3)
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

it('syncs a single model with attributes', function () {
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

it('syncs multiple models with attributes', function () {
    $users = User::factory(3)
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

it('replaces existing relationships when syncing', function () {
    $initialUsers = User::factory(2)
        ->create();

    $this->product->users()->attach($initialUsers, ['is_active' => false]);

    $this->assertDatabaseCount('product_user', 2);

    $newUsers = User::factory(3)
        ->create();

    $this->action->handle($this->product, $newUsers);

    $this->assertDatabaseCount('product_user', 3);

    $newUsers->each(function ($user) {
        $this->assertDatabaseHas('product_user', [
            'product_id' => $this->product->id,
            'user_id' => $user->id,
            'is_active' => false,
        ]);
    });

    $initialUsers->each(function ($user) {
        $this->assertDatabaseMissing('product_user', [
            'product_id' => $this->product->id,
            'user_id' => $user->id,
        ]);
    });
});

it('can sync with empty array to remove all relationships', function () {
    $users = User::factory(3)
        ->create();

    $this->product->users()->attach($users, ['is_active' => false]);

    $this->assertDatabaseCount('product_user', 3);

    $this->action->handle($this->product, []);

    $this->assertDatabaseCount('product_user', 0);
});
