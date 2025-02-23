<?php

declare(strict_types=1);

use Honed\Action\Creator;
use Honed\Action\PageAction;
use Honed\Action\Tests\Stubs\Product;
use Symfony\Component\HttpFoundation\Request;

beforeEach(function () {
    $this->test = PageAction::make('test');
});

it('has array representation', function () {
    expect($this->test->toArray())
        ->toBeArray()
        ->toEqual([
            'name' => 'test',
            'label' => 'Test',
            'type' => Creator::Page,
            'icon' => null,
            'extra' => [],
            'action' => false,
            'confirm' => null,
            'action' => false,
        ]);
});

it('has array representation with route', function () {
    expect($this->test->route('products.index')->toArray())
        ->toBeArray()
        ->toEqual([
            'name' => 'test',
            'label' => 'Test',
            'type' => Creator::Page,
            'icon' => null,
            'extra' => [],
            'action' => false,
            'confirm' => null,
            'action' => false,
            'href' => route('products.index'),
            'method' => Request::METHOD_GET,
        ]);
});

it('resolves', function () {
    $product = product();

    expect(PageAction::make('test')
        ->route(fn (Product $product) => route('products.show', $product))
        ->resolve(...params($product))
    )->toBeInstanceOf(PageAction::class)
        ->getLabel()->toBe('Test')
        ->routeToArray()->toEqual([
        'href' => route('products.show', $product),
        'method' => Request::METHOD_GET,
    ]);
});

it('executes', function () {
    $product = product();

    $this->test
        ->action(fn (Product $product) => $product->update(['name' => 'test']))
        ->execute($product);
    
    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'name' => 'test',
    ]);
});