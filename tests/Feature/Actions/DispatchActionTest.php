<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Event;
use Workbench\App\Actions\Product\DispatchProductCreated;
use Workbench\App\Events\ProductCreated;
use Workbench\App\Models\Product;

beforeEach(function () {
    $this->action = new DispatchProductCreated();

    $this->product = Product::factory()->create();

    Event::fake();
});

it('dispatches an event', function () {
    $this->action->handle($this->product);

    Event::assertDispatched(ProductCreated::class);
});
