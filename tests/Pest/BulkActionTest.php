<?php

declare(strict_types=1);

use Pest\Expectation;
use Honed\Action\Creator;
use Honed\Action\BulkAction;
use Illuminate\Support\Collection;
use Honed\Action\Tests\Stubs\Product;

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
        ->toEqual([
            'name' => 'test',
            'label' => 'Test',
            'type' => Creator::Bulk,
            'icon' => null,
            'extra' => [],
            'action' => false,
            'confirm' => null,
            'action' => false,
            'keepSelected' => false,
        ]);
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
