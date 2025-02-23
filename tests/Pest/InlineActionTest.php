<?php

declare(strict_types=1);

use Honed\Action\Creator;
use Honed\Action\InlineAction;
use Honed\Action\Tests\Fixtures\DestroyAction;
use Honed\Action\Tests\Stubs\Product;
use Symfony\Component\HttpFoundation\Request;
use Honed\Action\Tests\Fixtures\DestroyProduct;
use Illuminate\Http\RedirectResponse;

beforeEach(function () {
    $this->test = InlineAction::make('test');
});

it('has array representation', function () {
    expect($this->test->toArray())
        ->toBeArray()
        ->toEqual([
            'name' => 'test',
            'label' => 'Test',
            'type' => Creator::Inline,
            'icon' => null,
            'extra' => [],
            'action' => false,
            'confirm' => null,
            'default' => false,
            'action' => false,
        ]);
});

it('has array representation with route', function () {
    expect($this->test->route('products.index')->toArray())
        ->toBeArray()
        ->toEqual([
            'name' => 'test',
            'label' => 'Test',
            'type' => Creator::Inline,
            'icon' => null,
            'extra' => [],
            'action' => false,
            'confirm' => null,
            'default' => false,
            'action' => false,
            'href' => route('products.index'),
            'method' => Request::METHOD_GET,
        ]);
});

it('resolves', function () {
    $product = product();
    $named = ['record' => $product];
    $typed = [Product::class => $product];

    expect((new DestroyAction)->resolve($named, $typed))
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
            ->resolveLabel($named, $typed)->toBe('Destroy '.$this->product->name)
            ->getType()->toBe(Creator::Inline)
            ->hasAction()->toBeTrue();

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
});
