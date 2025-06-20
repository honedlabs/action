<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Workbench\App\Actions\Product\UpsertProduct;
use Workbench\App\Http\Requests\NameRequest;
use Workbench\App\Models\Product;

beforeEach(function () {
    $this->action = new UpsertProduct();

    $this->name = fake()->unique()->name();

    $this->assertDatabaseEmpty('products');
});

it('inserts a record', function () {
    Product::factory()->create();

    $values = Product::factory()
        ->create()
        ->only('id', 'name');

    $this->action->handle($values);

    $this->assertDatabaseCount('products', 2);
});

it('updates a record', function () {
    $product = Product::factory()->create();

    $this->action->handle([
        'id' => $product->id,
        'name' => $this->name,
    ]);

    $this->assertDatabaseCount('products', 1);

    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'name' => $this->name,
    ]);
});

it('upserts multiple records', function () {
    $product = Product::factory()->create();

    $this->assertDatabaseCount('products', 1);

    // Now upsert with the same name but different updateable fields
    $values = [
        [
            'id' => $product->id,
            'name' => $this->name,
        ],
        [
            'id' => 5,
            'name' => $this->name,
        ],
    ];

    $this->action->handle($values);

    $this->assertDatabaseCount('products', 2);

    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'name' => $this->name,
    ]);

    $this->assertDatabaseHas('products', [
        'id' => 5,
        'name' => $this->name,
    ]);
});

it('upserts a record with validated input', function () {
    $product = Product::factory()->create();

    $this->assertDatabaseCount('products', 1);

    $input = Validator::make([
        'id' => $product->id,
        'name' => $this->name,
    ], [
        'id' => 'required|integer',
        'name' => 'required|string|max:255',
    ]);

    $this->action->handle($input->safe());

    $this->assertDatabaseCount('products', 1);

    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'name' => $this->name,
    ]);
});

it('upserts a model with a form request', function () {
    $product = Product::factory()->create();

    $request = Request::create('/', 'POST', [
        'id' => $product->id,
        'name' => $this->name,
    ]);

    $this->app->instance('request', $request);

    $request = $this->app->make(NameRequest::class);

    $request->setContainer($this->app);

    $request->validateResolved();

    $this->action->handle($request);

    $this->assertDatabaseCount('products', 1);

    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'name' => $this->name,
    ]);
});
