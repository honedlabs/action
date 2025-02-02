<?php

declare(strict_types=1);

use Honed\Action\Creator;
use Honed\Action\InlineAction;
use Honed\Action\Tests\Stubs\DestroyAction;
use Honed\Action\Tests\Stubs\Product;

beforeEach(function () {
    $this->test = InlineAction::make('test');
});

it('makes', function () {
    expect($this->test)
        ->toBeInstanceOf(InlineAction::class);
});

it('has array representation', function () {
    expect($this->test->toArray())
        ->toBeArray()
        ->toHaveKeys(['name', 'label', 'type', 'icon', 'extra', 'default', 'action']);
});

it('has array representation with destination', function () {
    expect($this->test->to('test')->toArray())
        ->toBeArray()
        ->toHaveKeys(['name', 'label', 'type', 'icon', 'extra', 'default', 'action', 'href', 'method', 'tab']);
});

it('resolves', function () {
    $product = product();

    expect(DestroyAction::make()->resolve($product))
        ->toBeInstanceOf(DestroyAction::class)
        ->getLabel()->toBe('Destroy '.$product->name);
});

describe('executes', function () {
    beforeEach(function () {
        $this->product = product();
    });

    test('not without action', function () {
        expect($this->test->execute(product()))
            ->toBeNull();
    });

    test('with action callback', function () {
        $this->test->action(function (Product $product) {
            $product->update(['name' => 'test']);

            return inertia('Products/Show', [
                'product' => $product,
            ]);
        });

        expect($this->test->execute($this->product))
            ->toBeInstanceOf(\Inertia\Response::class);

        expect($this->product->name)
            ->toBe('test');
    });

    test('with handler', function () {
        $action = DestroyAction::make();

        expect($action)
            ->getName()->toBe('destroy')
            ->getLabel($this->product)->toBe('Destroy '.$this->product->name)
            ->getType()->toBe(Creator::Inline)
            ->hasAction()->toBeTrue();

        expect($action->execute($this->product))
            ->toBeNull();

        expect(Product::find($this->product->id))
            ->toBeNull();
    });
});
