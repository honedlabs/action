<?php

declare(strict_types=1);

use Honed\Action\PageAction;
use Honed\Action\ActionGroup;
use Honed\Action\InlineAction;
use Honed\Action\Tests\Stubs\Product;
use Honed\Action\Tests\Fixtures\ProductActions;

beforeEach(function () {
    $this->group = ActionGroup::make(PageAction::make('create'));
});

it('has resource', function () {
    expect($this->group)
        ->getResource()->toBeNull()
        ->resource(product())->toBe($this->group)
        ->getResource()->toBeInstanceOf(Product::class);
});

it('has route key name', function () {
    expect($this->group)
        ->getRouteKeyName()->toBe('action');
});;


it('resolves route binding', function () {
    expect($this->group)
        ->resolveRouteBinding($this->group->getRouteKey())
        ->toBeNull();

    $actions = ProductActions::make();

    expect($actions)
        ->resolveRouteBinding($actions->getRouteKey())
        ->toBeInstanceOf(ProductActions::class);

    expect($actions)
        ->resolveChildRouteBinding(ProductActions::class, $actions->getRouteKey())
        ->toBeInstanceOf(ProductActions::class);
});

it('has array representation', function () {
    expect($this->group->toArray())
        ->toBeArray()
        ->toHaveCount(3)
        ->toHaveKeys(['inline', 'bulk', 'page']);
});

it('has array representation with server actions', function () {
    expect(ProductActions::make()->toArray())
        ->toBeArray()
        ->toHaveCount(5)
        ->toHaveKeys(['id', 'endpoint', 'inline', 'bulk', 'page']);

    expect(ProductActions::make()->shouldNotExecute()->toArray())
        ->toHaveCount(3)
        ->toHaveKeys(['inline', 'bulk', 'page']);
});

it('has array representation with model', function () {
    $product = product();

    expect($this->group->actions(InlineAction::make('edit', fn ($record) => $record->name)))
        ->resource($product)->toBe($this->group)
        ->toArray()->scoped(fn ($group) => $group
            ->toBeArray()
            ->toHaveCount(3)
            ->toHaveKeys(['inline', 'bulk', 'page'])
            ->{'inline'}->scoped(fn ($actions) => $actions
                ->toBeArray()
                ->toHaveCount(1)
                ->{0}->scoped(fn ($action) => $action
                    ->toBeArray()
                    ->toHaveKey('label')
                    ->{'label'}->toBe($product->name)
                )
            )
        );
});