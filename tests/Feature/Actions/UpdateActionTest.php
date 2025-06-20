<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Workbench\App\Actions\Product\UpdateProduct;
use Workbench\App\Http\Requests\NameRequest;
use Workbench\App\Models\Product;

beforeEach(function () {
    $this->action = new UpdateProduct();

    $this->product = Product::factory()->create();

    $this->name = fake()->unique()->name();

    $this->input = [
        'name' => $this->name,
    ];

    $this->assertDatabaseCount('products', 1);
});

it('updates a model with validated input', function () {
    $input = Validator::make($this->input, [
        'name' => 'required|string|max:255',
    ]);

    $this->action->handle($this->product, $input->safe());

    $this->assertDatabaseCount('products', 1);

    $this->assertDatabaseHas('products', [
        'id' => $this->product->id,
        'name' => $this->name,
    ]);
});

it('updates a model with a form request', function () {
    $request = Request::create('/', 'POST', $this->input);

    $this->app->instance('request', $request);

    $request = $this->app->make(NameRequest::class);

    $request->setContainer($this->app);

    $request->validateResolved();

    $this->action->handle($this->product, $request);

    $this->assertDatabaseCount('products', 1);

    $this->assertDatabaseHas('products', [
        'id' => $this->product->id,
        'name' => $this->name,
    ]);
});

it('updates a model with array', function () {
    $this->action->handle($this->product, $this->input);

    $this->assertDatabaseCount('products', 1);

    $this->assertDatabaseHas('products', [
        'id' => $this->product->id,
        'name' => $this->name,
    ]);
});
