<?php

declare(strict_types=1);

use Honed\Action\Action;
use Honed\Action\Confirm;
use Illuminate\Support\Str;
use Honed\Action\InlineAction;
use Honed\Action\ActionFactory;
use Honed\Action\Tests\Stubs\Product;
use Symfony\Component\HttpFoundation\Request;
use Honed\Action\Tests\Fixtures\DestroyAction;

beforeEach(function () {
    // Using inline action for testing base class
    $this->action = InlineAction::make('test'); 
});

it('has array representation', function () {
    expect($this->action->toArray())
        ->toBeArray()
        ->toHaveKeys([
            'name',
            'label',
            'type',
            'icon',
            'extra',
            'actionable',
            'confirm',
            'route',
        ]);
});

it('has array representation with route', function () {
    expect($this->action->route('products.index')->toArray())
        ->toBeArray()
        ->toEqual([
            'name' => 'test',
            'label' => 'Test',
            'type' => ActionFactory::Inline,
            'icon' => null,
            'extra' => [],
            'actionable' => false,
            'confirm' => null,
            'default' => false,
            'route' => [
                'url' => route('products.index'),
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
            'actionable' => true,
            'confirm' => null,
            'default' => false,
            'route' => null,
        ]);
});

it('evaluates names', function () {
    expect($this->action->evaluate(fn ($confirm) => $confirm->title('test')))
        ->toBeInstanceOf(Confirm::class)
        ->getTitle()->toBe('test');

    $name = Str::random();
    expect($this->action->parameters(['name' => $name])
        ->evaluate(fn ($name) => $name))->toBe($name);
});

it('evaluates types', function () {
    expect($this->action->evaluate(fn (Confirm $c) => $c->title('test')))
        ->toBeInstanceOf(Confirm::class)
        ->getTitle()->toBe('test');

    // Dependency injection
    expect($this->action->evaluate(fn (Product $product) => $product))
        ->toBeInstanceOf(Product::class);
});


