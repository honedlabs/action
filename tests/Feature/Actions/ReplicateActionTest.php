<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;
use Workbench\App\Actions\Product\ReplicateProduct;
use Workbench\App\Http\Requests\NameRequest;
use Workbench\App\Models\Product;

beforeEach(function () {
    $this->action = new ReplicateProduct();

    $this->product = Product::factory()->create();

    $this->assertDatabaseHas('products', [
        'id' => $this->product->id,
    ]);

    $this->name = fake()->unique()->name();
});

it('replicates a model', function () {
    $replicated = $this->action->handle($this->product);

    $this->assertDatabaseCount('products', 2);

    $this->assertDatabaseHas('products', [
        'name' => $this->product->name,
        'price' => 0,
        'description' => $this->product->description,
    ]);
});

it('replicates a model with attributes', function () {
    $replicated = $this->action->handle($this->product, ['name' => $this->name]);

    $this->assertDatabaseCount('products', 2);

    $this->assertDatabaseHas('products', [
        'name' => $this->name,
        'price' => 0,
        'description' => $this->product->description,
    ]);
});

it('replicates a model with validated attributes', function () {
    $input = Validator::make([
        'name' => $this->name,
    ], [
        'name' => 'required|string|max:255',
    ]);

    $this->action->handle($this->product, $input->safe());

    $this->assertDatabaseCount('products', 2);

    $this->assertDatabaseHas('products', [
        'name' => $this->name,
        'price' => 0,
        'description' => $this->product->description,
    ]);
});

it('replicates a model with a form request', function () {
    $request = Request::create('/', 'POST', [
        'name' => $this->name,
    ]);

    $this->app->instance('request', $request);

    $request = $this->app->make(NameRequest::class);

    $request->setContainer($this->app);

    $request->validateResolved();

    $this->action->handle($this->product, $request);

    $this->assertDatabaseHas('products', [
        'name' => $this->name,
        'price' => 0,
        'description' => $this->product->description,
    ]);
});
