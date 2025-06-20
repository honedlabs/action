<?php

declare(strict_types=1);

use Workbench\App\Actions\Product\AssociateUser;
use Workbench\App\Models\Product;
use Workbench\App\Models\User;

beforeEach(function () {
    $this->action = new AssociateUser();

    $this->product = Product::factory()->create();

    $this->user = User::factory()->create();

    $this->assertDatabaseCount('products', 1);
    $this->assertDatabaseCount('users', 2); // 1 from factory, 1 from the test
});

it('associates a model to a parent', function () {
    expect($this->product->user->is($this->user))
        ->toBeFalse();

    $this->action->handle($this->product, $this->user);

    $this->assertDatabaseHas('products', [
        'id' => $this->product->id,
        'user_id' => $this->user->id,
    ]);

    expect($this->product->user->is($this->user))
        ->toBeTrue();
});

it('associates a key to a parent', function () {
    $this->action->handle($this->product, $this->user->getKey());

    $this->assertDatabaseHas('products', [
        'id' => $this->product->id,
        'user_id' => $this->user->id,
    ]);
});
