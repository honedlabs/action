<?php

declare(strict_types=1);

use Honed\Action\ActionFactory;
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
            'type' => ActionFactory::Page,
            'icon' => null,
            'extra' => [],
            'action' => false,
            'confirm' => null,
            'action' => false,
            'route' => null,
        ]);
});

it('has array representation with route', function () {
    expect($this->test->route('products.index')->toArray())
        ->toBeArray()
        ->toEqual([
            'name' => 'test',
            'label' => 'Test',
            'type' => ActionFactory::Page,
            'icon' => null,
            'extra' => [],
            'action' => false,
            'confirm' => null,
            'action' => false,
            'route' => [
                'href' => route('products.index'),
                'method' => Request::METHOD_GET,
            ],
        ]);
});

it('resolves to array', function () {
    $product = product();

    $action = PageAction::make('test')
        ->route(fn (Product $product) => route('products.show', $product));

    expect($action->resolveToArray(...params($product)))
        ->toEqual([
            'name' => 'test',
            'label' => 'Test',
            'type' => ActionFactory::Page,
            'icon' => null,
            'extra' => [],
            'action' => false,
            'confirm' => null,
            'route' => [
                'href' => route('products.show', $product),
                'method' => Request::METHOD_GET,
            ],
        ]);
});

it('calls query closure', function () {
    $product = product();

    $fn = fn ($query) => $query->where('id', $product->id);

    expect(PageAction::make('test'))
        ->hasQueryClosure()->toBeFalse()
        ->query($fn)->toBeInstanceOf(PageAction::class)
        ->hasQueryClosure()->toBeTrue();
});
