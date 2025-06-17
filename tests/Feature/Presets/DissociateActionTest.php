<?php

declare(strict_types=1);

use Workbench\App\Actions\Product\DissociateUser;
use Workbench\App\Models\Product;
use Workbench\App\Models\User;

beforeEach(function () {
    $this->action = new DissociateUser();

    $this->product = Product::factory()->create();
});

it('dissociates a model from its parent', function () {
    $user = User::factory()
        ->for($this->product)
        ->create();

    expect($user->product_id)->toBe($this->product->id);

    $this->action->handle($user);

    $user->refresh();

    expect($user->product_id)->toBeNull();
});

it('dissociates a model that is already dissociated', function () {
    $user = User::factory()
        ->create(['product_id' => null]);

    expect($user->product_id)->toBeNull();

    $this->action->handle($user);

    $user->refresh();

    expect($user->product_id)->toBeNull();
});

it('dissociates a model and saves the changes', function () {
    $user = User::factory()
        ->for($this->product)
        ->create();

    expect($user->product_id)->toBe($this->product->id);

    $originalUpdatedAt = $user->updated_at;

    // Sleep to ensure timestamp changes
    sleep(1);

    $this->action->handle($user);

    $user->refresh();

    expect($user->product_id)->toBeNull();
    expect($user->updated_at)->not->toEqual($originalUpdatedAt);
});
