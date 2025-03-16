<?php

declare(strict_types=1);

use Honed\Action\ActionFactory;
use Honed\Action\InlineAction;
use Honed\Action\Tests\Fixtures\DestroyAction;
use Honed\Action\Tests\Stubs\Product;
use Symfony\Component\HttpFoundation\Request;

beforeEach(function () {
    $this->test = InlineAction::make('test');
});

it('can be set as default', function () {
    expect($this->test)
        ->default()->toBe($this->test)
        ->isDefault()->toBeTrue();
});

it('has array representation', function () {
    expect($this->test->toArray())
        ->toBeArray()
        ->toEqual([
            'name' => 'test',
            'label' => 'Test',
            'type' => ActionFactory::Inline,
            'icon' => null,
            'extra' => [],
            'action' => false,
            'confirm' => null,
            'default' => false,
            'route' => null,
        ]);
});

it('has array representation with route', function () {
    expect($this->test->route('products.index')->toArray())
        ->toBeArray()
        ->toEqual([
            'name' => 'test',
            'label' => 'Test',
            'type' => ActionFactory::Inline,
            'icon' => null,
            'extra' => [],
            'action' => false,
            'confirm' => null,
            'default' => false,
            'action' => false,
            'route' => [
                'href' => route('products.index'),
                'method' => Request::METHOD_GET,
            ],
        ]);
});

it('resolves to array', function () {
    $product = product();

    expect((new DestroyAction)->resolveToArray(...params($product)))
        ->toEqual([
            'name' => 'destroy',
            'label' => 'Destroy '.$product->name,
            'type' => ActionFactory::Inline,
            'icon' => null,
            'extra' => [],
            'action' => true,
            'confirm' => null,
            'default' => false,
            'route' => null,
        ]);
});