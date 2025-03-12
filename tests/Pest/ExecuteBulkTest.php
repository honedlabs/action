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

    $this->action->chunkById();

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

    $this->action->chunkById();

    $fn = fn (Product $q) => $q->update(['name' => $name]);

    $this->action->action($fn)->execute(Product::query());

    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'name' => $name,
    ]);
});

it('executes on chunked collection', function () {
    $product = product();

    $this->action->chunk();

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

    $this->action->chunk();

    $fn = fn (Product $q) => $q->update(['name' => $name]);

    $this->action->action($fn)->execute(Product::query());

    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'name' => $name,
    ]);
});

it('errors if chunking with builder', function () {
    $fn = fn (Builder $q) => $q->update(['name' => 'test']);

    $this->action->action($fn)
        ->chunk()
        ->execute(Product::query());

})->throws(\RuntimeException::class);
