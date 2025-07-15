<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use Illuminate\Support\ValidatedInput;
use Workbench\App\Actions\Product\StoreUserProduct;
use Workbench\App\Http\Requests\NameRequest;
use Workbench\App\Models\User;

beforeEach(function () {
    $this->action = new StoreUserProduct();

    $this->user = User::factory()->create();

    $this->name = fake()->unique()->name();

    $this->input = [
        'name' => $this->name,
    ];

    $this->assertDatabaseEmpty('products');
});

it('stores a model with ValidatedInput', function () {
    $input = new ValidatedInput($this->input);

    $product = $this->action->handle($this->user, $input);

    $this->assertDatabaseCount('products', 1);

    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'name' => $this->name,
        'user_id' => $this->user->id,
    ]);
});

it('stores a model with form request', function () {
    $request = Request::create('/', 'POST', $this->input);

    $this->app->instance('request', $request);

    $request = $this->app->make(NameRequest::class);

    $request->setContainer($this->app);

    $request->validateResolved();

    $product = $this->action->handle($this->user, $request);

    $this->assertDatabaseCount('products', 1);

    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'name' => $this->name,
        'user_id' => $this->user->id,
    ]);
});

it('stores a model with array', function () {
    $product = $this->action->handle($this->user, $this->input);

    $this->assertDatabaseCount('products', 1);

    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'name' => $this->name,
        'user_id' => $this->user->id,
    ]);
});
