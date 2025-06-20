<?php

declare(strict_types=1);

use Workbench\App\Actions\Product\DissociateUser;
use Workbench\App\Models\Product;
use Workbench\App\Models\User;

beforeEach(function () {
    $this->action = new DissociateUser();

    $this->user = User::factory()->create();
    $this->product = Product::factory()
        ->for($this->user)
        ->create();

    $this->assertDatabaseCount('products', 1);
    $this->assertDatabaseCount('users', 1);
});

it('dissociates a model from its parent', function () {
    expect($this->product->user->is($this->user))
        ->toBeTrue();

    $this->action->handle($this->product);

    $this->assertDatabaseHas('products', [
        'id' => $this->product->id,
        'user_id' => null,
    ]);
});

it('dissociates a model that is already dissociated', function () {
    $this->product->user_id = null;
    $this->product->save();

    $this->action->handle($this->product);

    $this->assertDatabaseHas('products', [
        'id' => $this->product->id,
        'user_id' => null,
    ]);
});
