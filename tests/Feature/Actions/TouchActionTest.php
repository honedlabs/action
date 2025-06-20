<?php

declare(strict_types=1);

use Carbon\Carbon;
use Workbench\App\Actions\Product\TouchProduct;
use Workbench\App\Models\Product;

beforeEach(function () {
    $this->action = new TouchProduct();

    $date = Carbon::parse('2000-01-01 00:00:00');

    $this->product = Product::factory()->create([
        'created_at' => $date,
        'updated_at' => $date,
    ]);

    $this->assertDatabaseHas('products', [
        'id' => $this->product->id,
        'created_at' => $date,
        'updated_at' => $date,
    ]);
});

it('touches the model', function () {
    $this->action->handle($this->product);

    $this->assertDatabaseHas('products', [
        'id' => $this->product->id,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
});
