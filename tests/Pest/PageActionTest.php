<?php

declare(strict_types=1);

use Honed\Action\PageAction;
use Honed\Action\Tests\Stubs\Product;

beforeEach(function () {
    $this->test = PageAction::make('test');
});

it('makes', function () {
    expect($this->test)
        ->toBeInstanceOf(PageAction::class);
});

it('has array representation', function () {
    expect($this->test->toArray())
        ->toBeArray()
        ->toHaveKeys(['name', 'label', 'type', 'icon', 'extra']);
});

it('has array representation with destination', function () {
    expect($this->test->to('test')->toArray())
        ->toBeArray()
        ->toHaveKeys(['name', 'label', 'type', 'icon', 'extra', 'href', 'method', 'tab']);
});

it('resolves', function () {
    $product = product();

    expect(PageAction::make('test')
            ->to(fn (Product $product) => route('products.show', $product))
            ->resolve($product)
        )->toBeInstanceOf(PageAction::class)
        ->getLabel()->toBe('Test')
        ->getDestination()->scoped(fn ($destination) => $destination
            ->get()->toBe(route('products.show', $product))
            ->getMethod()->toBe('GET')
            ->getTab()->toBeFalse());
});
