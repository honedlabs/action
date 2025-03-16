<?php

declare(strict_types=1);

use Honed\Action\BulkAction;
use Honed\Action\ActionFactory;
use Honed\Action\Tests\Stubs\Product;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\Request;

beforeEach(function () {
    $this->action = BulkAction::make('test');

    foreach (range(1, 10) as $i) {
        product();
    }
});

it('keeps selected', function () {
    expect($this->action)
        ->keepsSelected()->toBeFalse()
        ->keepSelected()->toBe($this->action)
        ->keepsSelected()->toBeTrue();
});

it('has array representation', function () {
    expect($this->action->toArray())
        ->toBeArray()
        ->toEqual([
            'name' => 'test',
            'label' => 'Test',
            'type' => ActionFactory::Bulk,
            'icon' => null,
            'extra' => [],
            'action' => false,
            'confirm' => null,
            'action' => false,
            'keepSelected' => false,
            'route' => null,
        ]);
});

it('resolves to array', function () {
    $product = product();

    $action = BulkAction::make('test')
        ->route(fn (Product $product) => route('products.show', $product));

    expect($action->resolveToArray(...params($product)))
        ->toEqual([
            'name' => 'test',
            'label' => 'Test',
            'type' => ActionFactory::Bulk,
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

    expect(BulkAction::make('test'))
        ->hasQueryClosure()->toBeFalse()
        ->query($fn)->toBeInstanceOf(BulkAction::class)
        ->hasQueryClosure()->toBeTrue();
});