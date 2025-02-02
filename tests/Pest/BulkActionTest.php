<?php

declare(strict_types=1);

use Honed\Action\BulkAction;
use Honed\Action\Tests\Stubs\Product;
use Illuminate\Support\Collection;
use Pest\Expectation;

beforeEach(function () {
    $this->action = BulkAction::make('test');

    foreach (range(1, 10) as $i) {
        product();
    }
});

it('makes', function () {
    expect($this->action)
        ->toBeInstanceOf(BulkAction::class);
});

it('has array representation', function () {
    expect($this->action->toArray())
        ->toBeArray()
        ->toHaveKeys(['name', 'label', 'type', 'icon', 'extra', 'action']);
});

describe('executes', function () {
    beforeEach(function () {
        $this->builder = Product::query();
    });

    test('not without action', function () {
        expect($this->action->execute(Product::query()))
            ->toBeNull();
    });

    test('with collection callback', function () {
        $this->action->action(fn (Collection $collection) => $collection
            ->each(fn (Product $product) => $product->update(['name' => 'Updated']))
        )->execute($this->builder);

        expect(Product::query()->get())
            ->each(function (Expectation $product) {
                expect($product->value->name)->toBe('Updated');
            });
    });
});
