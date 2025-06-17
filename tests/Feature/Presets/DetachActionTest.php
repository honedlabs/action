<?php

declare(strict_types=1);

use Workbench\App\Actions\Product\DetachUser;
use Workbench\App\Models\Product;
use Workbench\App\Models\User;

beforeEach(function () {
    $this->action = new DetachUser();

    $this->product = Product::factory()->create();

    $this->assertDatabaseEmpty('product_user');
});

it('detaches a single model', function () {
    $user = User::factory()
        ->hasAttached($this->product, relationship: 'purchasedProducts')
        ->create();

    $this->assertDatabaseHas('product_user', [
        'product_id' => $this->product->id,
        'user_id' => $user->id,
    ]);

    $this->action->handle($this->product, $user);

    $this->assertDatabaseEmpty('product_user');
});

it('detaches multiple models', function () {
    $users = User::factory()
        ->count(3)
        ->hasAttached($this->product, relationship: 'purchasedProducts')
        ->create();

    $this->assertDatabaseCount('product_user', 3);

    $this->action->handle($this->product, $users);

    $this->assertDatabaseEmpty('product_user');
});

it('detaches a single key', function () {
    $user = User::factory()
        ->hasAttached($this->product, relationship: 'purchasedProducts')
        ->create();

    $this->assertDatabaseHas('product_user', [
        'product_id' => $this->product->id,
        'user_id' => $user->id,
    ]);

    $this->action->handle($this->product, $user->getKey());

    $this->assertDatabaseEmpty('product_user');
});

it('detaches multiple keys', function () {
    $users = User::factory()
        ->count(3)
        ->hasAttached($this->product, relationship: 'purchasedProducts')
        ->create();

    $this->assertDatabaseCount('product_user', 3);

    $ids = $users->map(fn ($user) => $user->getKey());

    $this->action->handle($this->product, $ids);

    $this->assertDatabaseEmpty('product_user');
});

it('detaches only specified models when others exist', function () {
    $usersToDetach = User::factory()
        ->count(2)
        ->hasAttached($this->product, relationship: 'purchasedProducts')
        ->create();

    $usersToKeep = User::factory()
        ->count(2)
        ->hasAttached($this->product, relationship: 'purchasedProducts')
        ->create();

    $this->assertDatabaseCount('product_user', 4);

    $this->action->handle($this->product, $usersToDetach);

    $this->assertDatabaseCount('product_user', 2);

    $usersToKeep->each(function ($user) {
        $this->assertDatabaseHas('product_user', [
            'product_id' => $this->product->id,
            'user_id' => $user->id,
        ]);
    });

    $usersToDetach->each(function ($user) {
        $this->assertDatabaseMissing('product_user', [
            'product_id' => $this->product->id,
            'user_id' => $user->id,
        ]);
    });
});
