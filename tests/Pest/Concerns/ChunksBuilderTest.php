<?php

declare(strict_types=1);

use Honed\Action\Concerns\Support\ChunksBuilder;
use Honed\Action\Tests\Stubs\Product;

class ChunksBuilderTest
{
    use ChunksBuilder;
}

beforeEach(function () {
    $this->test = new ChunksBuilderTest;
});

it('does not chunk by default', function () {
    expect($this->test)
        ->chunks()->toBeFalse()
        ->getChunkSize()->toBe(1000)
        ->chunksById()->toBeTrue();

    expect($this->test->chunkRecords(Product::query(), fn ($product) => $product->id))
        ->toBeFalse();
});

it('chunks', function () {
    expect($this->test->chunk())
        ->toBeInstanceOf(ChunksBuilderTest::class)
        ->chunks()->toBeTrue()
        ->getChunkSize()->toBe(1000)
        ->chunksById()->toBeTrue();
});

it('chunks by id', function () {
    populate(100);

    expect($this->test->chunk()->chunkRecords(
        Product::query(),
        fn ($products) => $products->each(fn ($product) => $product->update(['name' => 'test'])),
        false
    ))->toBeTrue();

    expect(Product::query()->get())
        ->each(function ($product) {
            expect($product->value->name)->toBe('test');
        });
});

it('chunks by custom callback', function () {
    populate(100);

    expect($this->test->chunk(500, false)
        ->chunkRecords(
            Product::query()->orderBy('public_id'),
            fn ($products) => $products->each(fn ($product) => $product->update(['name' => 'test'])),
            false
        ))->toBeTrue();

    expect(Product::query()->get())
        ->each(function ($product) {
            expect($product->value->name)->toBe('test');
        });
});

it('chunks by model', function () {
    populate(100);

    expect($this->test->chunk()
        ->chunkRecords(
            Product::query(),
            fn ($product) => $product->update(['name' => 'test']),
            true
        ))->toBeTrue();

    expect(Product::query()->get())
        ->each(function ($product) {
            expect($product->value->name)->toBe('test');
        });
});
