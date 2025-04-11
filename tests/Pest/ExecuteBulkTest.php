<?php

declare(strict_types=1);

use Honed\Action\BulkAction;
use Illuminate\Support\Collection;
use Honed\Action\Tests\Stubs\Product;
use Illuminate\Database\Eloquent\Builder;

beforeEach(function () {
    $this->action = BulkAction::make('test');
});

it('executes on builder', function () {
    $product = product();

    $fn = fn (Builder $q) => $q->update(['name' => 'test']);

    $this->action->action($fn)->execute(Product::query());
    
    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'name' => 'test',
    ]); 
});

it('executes on model', function () {
    $product = product();

    $fn = fn (Product $p) => $p->makeFree();

    $this->action->action($fn)->execute($product);

    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'price' => 0,
    ]);
});

it('executes on collection', function () {
    $product = product();

    $fn = fn (Collection $p) => $p->each(fn (Product $p) => $p->makeFree());

    $this->action->action($fn)->execute(Product::query());

    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'price' => 0,
    ]);
});

it('executes on chunked by id collection', function () {
    $product = product();

    $this->action->chunksById();

    $fn = fn (Collection $p) => $p->each(fn (Product $p) => $p->makeFree());

    $this->action->action($fn)->execute(Product::query());

    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'price' => 0,
    ]);
});

it('executes on chunked by id models', function () {
    $product = product();
    $name = 'test';

    $this->action->chunksById();

    $fn = fn (Product $q) => $q->update(['name' => $name]);

    $this->action->action($fn)->execute(Product::query());

    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'name' => $name,
    ]);
});

it('executes on chunked collection', function () {
    $product = product();

    $this->action->chunks();

    $fn = fn (Collection $p) => $p->each(fn (Product $p) => $p->makeFree());

    $this->action->action($fn)->execute(Product::query());

    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'price' => 0,
    ]);
});

it('executes on chunked models', function () {
    $product = product();
    $name = 'test';

    $this->action->chunks();

    $fn = fn (Product $q) => $q->update(['name' => $name]);

    $this->action->action($fn)->execute(Product::query());

    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'name' => $name,
    ]);
});

it('modifies query', function () {
    foreach (range(1, 10) as $i) {
        product();
    }

    $modify = fn (Builder $q) => $q->where('id', '>', 5);
    $fn = fn (Collection $p) => $p->each(fn (Product $p) => $p->makeFree());

    $this->action
        ->query($modify)
        ->action($fn)
        ->execute(Product::query());

    // Products with id > 5 should have price = 0
    for ($i = 6; $i <= 10; $i++) {
        $this->assertDatabaseHas('products', [
            'id' => $i,
            'price' => 0,
        ]);
    }

    // Products with id <= 5 should not have price = 0
    for ($i = 1; $i <= 5; $i++) {
        $this->assertDatabaseHas('products', [
            'id' => $i,
        ]);
        $this->assertDatabaseMissing('products', [
            'id' => $i,
            'price' => 0,
        ]);
    }
});

it('errors if chunking with builder', function () {
    $fn = fn (Builder $q) => $q->update(['name' => 'test']);

    $this->action->action($fn)
        ->chunks()
        ->execute(Product::query());

})->throws(\RuntimeException::class);
