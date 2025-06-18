<?php

declare(strict_types=1);

use Workbench\App\Actions\Product\RestoreProduct;
use Workbench\App\Models\Product;

beforeEach(function () {
    $this->action = new RestoreProduct();

    $this->product = Product::factory()->create();

    $this->assertDatabaseCount('products', 1);
});

it('does not restore a model that is not soft deleted', function () {
    $this->action->handle($this->product);

    $this->product->refresh();

    $this->assertNotSoftDeleted('products', [
        'id' => $this->product->id,
    ]);
});

it('restores a soft deleted model', function () {
    $this->product->delete();

    $this->assertSoftDeleted('products', [
        'id' => $this->product->id,
    ]);

    $this->action->handle($this->product);

    $this->assertNotSoftDeleted('products', [
        'id' => $this->product->id,
    ]);
});
