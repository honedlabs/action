<?php

declare(strict_types=1);

use Honed\Action\BulkAction;
use Illuminate\Support\Collection;
use Honed\Action\Tests\Stubs\Product;

beforeEach(function () {
    $this->product = product();
    $this->query = Product::query();
    $this->test = BulkAction::make('test')->chunk();
});
it('can chunk', function () {
    expect($this->test)
        ->isChunked()->toBeTrue()
        ->chunksById()->toBeTrue()
        ->getChunkSize()->toBe(200);
});

it('can execute chunked', function () {
    $fn = fn (Collection $collection) => $collection->each(function (Product $product) {
        $product->update(['name' => 'test']);
    });

    $this->test->action($fn)->execute($this->query);

    $this->assertDatabaseHas('products', [
        'id' => $this->product->id,
        'name' => 'test',
    ]);
});

it('can execute chunked on record', function () {
    $fn = fn (Product $product) => $product->update(['name' => 'test']);

    $this->test->action($fn)->onRecord()->execute($this->query);

    $this->assertDatabaseHas('products', [
        'id' => $this->product->id,
        'name' => 'test',
    ]);
});

it('can customise the execution', function () {
    $fn = fn (Product $product) => $product->update(['name' => 'test']);
    
    $this->test->action($fn)
        ->onRecord()
        ->chunkById(false)
        ->chunkSize(1000)
        ->execute($this->query);

    expect($this->test)
        ->actsOnRecord()->toBeTrue()
        ->isChunked()->toBeTrue()
        ->chunksById()->toBeFalse()
        ->getChunkSize()->toBe(1000);

    $this->assertDatabaseHas('products', [
        'id' => $this->product->id,
        'name' => 'test',
    ]);
});
