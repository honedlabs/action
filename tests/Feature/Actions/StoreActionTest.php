<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use Illuminate\Support\ValidatedInput;
use Workbench\App\Actions\Product\StoreProduct;
use Workbench\App\Http\Requests\NameRequest;

beforeEach(function () {
    $this->action = new StoreProduct();

    $this->name = fake()->unique()->name();

    $this->input = [
        'name' => $this->name,
    ];

    $this->assertDatabaseEmpty('products');
});

it('stores a model with ValidatedInput', function () {
    $input = new ValidatedInput($this->input);

    $product = $this->action->handle($input);

    $this->assertDatabaseCount('products', 1);

    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'name' => $this->name,
    ]);
});

it('stores a model with form request', function () {
    $request = Request::create('/', 'POST', $this->input);

    $this->app->instance('request', $request);

    $request = $this->app->make(NameRequest::class);

    $request->setContainer($this->app);

    $request->validateResolved();

    $product = $this->action->handle($request);

    $this->assertDatabaseCount('products', 1);

    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'name' => $this->name,
    ]);
});

it('stores a model with array', function () {
    $product = $this->action->handle($this->input);

    $this->assertDatabaseCount('products', 1);

    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'name' => $this->name,
    ]);
});
