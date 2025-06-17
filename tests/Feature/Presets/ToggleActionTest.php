<?php

declare(strict_types=1);

use Workbench\App\Actions\Product\ToggleUsers;
use Workbench\App\Models\Product;
use Workbench\App\Models\User;

beforeEach(function () {
    $this->action = new ToggleUsers();

    $this->product = Product::factory()->create();

    $this->assertDatabaseEmpty('product_user');
});

it('toggles a single model (attach when not attached)', function () {
    $user = User::factory()
        ->create();

    $this->action->handle($this->product, $user);

    $this->assertDatabaseCount('product_user', 1);

    $this->assertDatabaseHas('product_user', [
        'product_id' => $this->product->id,
        'user_id' => $user->id,
        'is_active' => false,
    ]);
});

it('toggles a single model (detach when already attached)', function () {
    $user = User::factory()
        ->create();

    // First attach the user
    $this->product->users()->attach($user->id, ['is_active' => false]);

    $this->assertDatabaseCount('product_user', 1);

    // Now toggle should detach
    $this->action->handle($this->product, $user);

    $this->assertDatabaseEmpty('product_user');
});

it('toggles multiple models (attach when not attached)', function () {
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

it('toggles multiple models (detach when already attached)', function () {
    $users = User::factory()
        ->count(3)
        ->create();

    // First attach all users
    $users->each(function ($user) {
        $this->product->users()->attach($user->id, ['is_active' => false]);
    });

    $this->assertDatabaseCount('product_user', 3);

    // Now toggle should detach all
    $this->action->handle($this->product, $users);

    $this->assertDatabaseEmpty('product_user');
});

it('toggles multiple models (mixed attach and detach)', function () {
    $users = User::factory()
        ->count(3)
        ->create();

    // Attach the first user only
    $this->product->users()->attach($users->first()->id, ['is_active' => false]);

    $this->assertDatabaseCount('product_user', 1);

    // Toggle all users - should detach first, attach others
    $this->action->handle($this->product, $users);

    $this->assertDatabaseCount('product_user', 2);

    // First user should be detached
    $this->assertDatabaseMissing('product_user', [
        'product_id' => $this->product->id,
        'user_id' => $users->first()->id,
    ]);

    // Other users should be attached
    $users->skip(1)->each(function ($user) {
        $this->assertDatabaseHas('product_user', [
            'product_id' => $this->product->id,
            'user_id' => $user->id,
            'is_active' => false,
        ]);
    });
});

it('toggles a single key (attach when not attached)', function () {
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

it('toggles a single key (detach when already attached)', function () {
    $user = User::factory()
        ->create();

    // First attach the user
    $this->product->users()->attach($user->id, ['is_active' => false]);

    $this->assertDatabaseCount('product_user', 1);

    // Now toggle using key should detach
    $this->action->handle($this->product, $user->getKey());

    $this->assertDatabaseEmpty('product_user');
});

it('toggles multiple keys (attach when not attached)', function () {
    $users = User::factory()
        ->count(3)
        ->create();

    $ids = $users->map(fn ($user) => $user->getKey());

    $this->action->handle($this->product, $ids->toArray());

    $this->assertDatabaseCount('product_user', 3);

    $users->each(function ($user) {
        $this->assertDatabaseHas('product_user', [
            'product_id' => $this->product->id,
            'user_id' => $user->id,
            'is_active' => false,
        ]);
    });
});

it('toggles multiple keys (detach when already attached)', function () {
    $users = User::factory()
        ->count(3)
        ->create();

    // First attach all users
    $users->each(function ($user) {
        $this->product->users()->attach($user->id, ['is_active' => false]);
    });

    $this->assertDatabaseCount('product_user', 3);

    $ids = $users->map(fn ($user) => $user->getKey());

    // Now toggle should detach all
    $this->action->handle($this->product, $ids->toArray());

    $this->assertDatabaseEmpty('product_user');
});

it('toggles a single model with attributes (attach when not attached)', function () {
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

it('toggles a single model with attributes (detach when already attached)', function () {
    $user = User::factory()
        ->create();

    // First attach the user with different attributes
    $this->product->users()->attach($user->id, ['is_active' => false]);

    $this->assertDatabaseCount('product_user', 1);

    // Toggle with new attributes should detach (attributes don't affect detach)
    $this->action->handle($this->product, $user, ['is_active' => true]);

    $this->assertDatabaseEmpty('product_user');
});

it('toggles multiple models with attributes (attach when not attached)', function () {
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

it('toggles multiple models with attributes (detach when already attached)', function () {
    $users = User::factory()
        ->count(3)
        ->create();

    // First attach all users
    $users->each(function ($user) {
        $this->product->users()->attach($user->id, ['is_active' => false]);
    });

    $this->assertDatabaseCount('product_user', 3);

    // Toggle with attributes should detach all
    $this->action->handle($this->product, $users, ['is_active' => true]);

    $this->assertDatabaseEmpty('product_user');
});
