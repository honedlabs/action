<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Event;
use Workbench\App\Events\ProductCreated;
use Workbench\App\Models\Product;
use Workbench\App\Processes\FailProcess;
use Workbench\App\Processes\ProductProcess;

beforeEach(function () {
    Event::fake();

    $this->data = [
        'name' => 'Name',
        'description' => 'Description',
    ];
});

it('handles', function () {
    expect(ProductProcess::make()->handle($this->data))
        ->toBeInstanceOf(Product::class);

    $this->assertDatabaseHas('products', [
        'name' => 'Name',
        'description' => 'Description',
    ]);

    Event::assertDispatched(ProductCreated::class);
});

it('handles with error throwing', function () {
    FailProcess::make()->run($this->data);
})->throws(Exception::class);

it('handles with error handling', function () {
    expect(FailProcess::make()->handle($this->data))
        ->toBeFalse();
});
