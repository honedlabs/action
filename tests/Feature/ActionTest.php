<?php

declare(strict_types=1);

use Honed\Action\Action;
use Honed\Action\Confirm;
use Honed\Action\InlineAction;
use Honed\Action\Support\Constants;
use Honed\Action\Tests\Fixtures\DestroyAction;
use Honed\Action\Tests\Stubs\Product;
use Honed\Core\Parameters;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Request;

beforeEach(function () {
    // Using inline action for testing base class
    $this->action = InlineAction::make('test');
});

it('has implicit route bindings', function () {
    $product = product();

    [$named, $typed] = Parameters::model($product);

    $this->action->route('products.show', '{product}');

    expect($this->action->toArray($named, $typed))
        ->toHaveKey('route')
        ->{'route'}->scoped(fn ($route) => $route
            ->toHaveKey('url')
            ->{'url'}->toBe(route('products.show', $product))
        );
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
            'type' => Constants::INLINE,
            'icon' => null,
            'extra' => null,
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

    expect((new DestroyAction)->toArray(...params($product)))
        ->toEqual([
            'name' => 'destroy',
            'label' => 'Destroy '.$product->name,
            'type' => Constants::INLINE,
            'icon' => null,
            'extra' => null,
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
