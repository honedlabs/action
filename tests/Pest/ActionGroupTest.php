<?php

declare(strict_types=1);

use Honed\Action\PageAction;
use Honed\Action\ActionGroup;
use Honed\Action\Tests\Fixtures\ProductActions;

beforeEach(function () {
    $this->group = ActionGroup::make(PageAction::make('create'));
});

it('has route key name', function () {
    expect($this->group)
        ->getRouteKeyName()->toBe('action');
});

it('has model', function () {
    expect($this->group)
        ->for(product())->toBe($this->group);
});

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

    expect(ProductActions::make()->shouldExecute(false)->toArray())
        ->toHaveCount(3)
        ->toHaveKeys(['inline', 'bulk', 'page']);
});