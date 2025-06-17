<?php

declare(strict_types=1);

use Workbench\App\Actions\Product\AssociateUser;
use Workbench\App\Models\Product;
use Workbench\App\Models\User;

beforeEach(function () {
    $this->action = new AssociateUser();

    $this->product = Product::factory()->create();

    $this->user = User::factory()->create();
});

it('handles model association', function () {
    expect($this->product->user->is($this->user))
        ->toBeFalse();

    $this->action->handle($this->product, $this->user);

    $this->product->refresh();

    dd($this->product);
    expect($this->product->user->is($this->user))
        ->toBeTrue();
})->todo();
