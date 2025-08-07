<?php

declare(strict_types=1);

use Workbench\App\Actions\Product\BulkUpdateProducts;
use Workbench\App\Models\Product;

beforeEach(function () {
    $this->action = new BulkUpdateProducts();

    $this->products = Product::factory(3)->create();
});

it('updates id', function () {
    $id = $this->products->first()->id;
    $this->action->handle($id, ['name' => 'Bulk']);

    $this->assertDatabaseHas('products', [
        'id' => $id,
        'name' => 'Bulk',
    ]);
});

it('updates array', function () {
    $ids = $this->products->pluck('id')->all();
    $this->action->handle($ids, ['name' => 'Bulk']);

    foreach ($ids as $id) {
        $this->assertDatabaseHas('products', [
            'id' => $id,
            'name' => 'Bulk',
        ]);
    }
});

it('updates collection', function () {
    $ids = $this->products->modelKeys();
    $this->action->handle($ids, ['name' => 'Bulk']);

    foreach ($ids as $id) {
        $this->assertDatabaseHas('products', [
            'id' => $id,
            'name' => 'Bulk',
        ]);
    }
});

it('updates eloquent collection', function () {
    $this->action->handle($this->products, ['name' => 'Bulk']);

    foreach ($this->products as $product) {
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Bulk',
        ]);
    }
});
