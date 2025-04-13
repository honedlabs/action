<?php

declare(strict_types=1);

use Honed\Action\InlineAction;
use Honed\Action\Support\Constants;
use Honed\Action\Tests\Fixtures\DestroyAction;
use Honed\Action\Tests\Fixtures\DestroyProduct;
use Honed\Action\Tests\Stubs\Product;
use Illuminate\Http\RedirectResponse;

beforeEach(function () {
    $this->test = InlineAction::make('test');
    $this->product = product();
});

test('not without action', function () {
    expect($this->test->execute(product()))
        ->toBeNull();

    $this->assertDatabaseHas('products', [
        'id' => $this->product->id,
    ]);
});

test('with callback', function () {
    $this->test->action(function (Product $product) {
        $product->update(['name' => 'test']);

        return inertia('Products/Show', [
            'product' => $product,
        ]);
    });

    expect($this->test->execute($this->product))
        ->toBeInstanceOf(\Inertia\Response::class);

    $this->assertDatabaseHas('products', [
        'id' => $this->product->id,
        'name' => 'test',
    ]);
});

test('with handler', function () {
    $action = new DestroyAction;

    $named = ['product' => $this->product];

    $typed = [Product::class => $this->product];

    expect($action)
        ->getName()->toBe('destroy')
        ->getLabel(...params($this->product))->toBe('Destroy '.$this->product->name)
        ->getType()->toBe(Constants::INLINE)
        ->isActionable()->toBeTrue();

    $action->execute($this->product);

    $this->assertDatabaseMissing('products', [
        'id' => $this->product->id,
    ]);
});

test('with class-string action', function () {
    expect($this->test->action(DestroyProduct::class))
        ->execute($this->product)
        ->toBeInstanceOf(RedirectResponse::class);

    $this->assertDatabaseMissing('products', [
        'id' => $this->product->id,
    ]);
});
