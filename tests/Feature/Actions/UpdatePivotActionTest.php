<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Workbench\App\Actions\Product\UpdateProductUser;
use Workbench\App\Http\Requests\ActiveRequest;
use Workbench\App\Models\Product;
use Workbench\App\Models\User;

beforeEach(function () {
    $this->action = new UpdateProductUser();

    $this->product = Product::factory()->create();

    $this->user = User::factory()->create();

    $this->product->users()->attach($this->user);

    $this->pivot = $this->product->users()->first();

    $this->input = [
        'is_active' => true,
    ];
});

it('updates pivot with validated input', function () {
    $input = Validator::make($this->input, [
        'is_active' => 'required|boolean',
    ]);

    $this->action->handle($this->pivot, $input->safe());

    $this->assertDatabaseHas('product_user', [
        'product_id' => $this->product->id,
        'user_id' => $this->user->id,
        'is_active' => true,
    ]);
});

it('updates a model with a form request', function () {
    $request = Request::create('/', 'POST', $this->input);

    $this->app->instance('request', $request);

    $request = $this->app->make(ActiveRequest::class);

    $request->setContainer($this->app);

    $request->validateResolved();

    $this->action->handle($this->pivot, $request);

    $this->assertDatabaseHas('product_user', [
        'product_id' => $this->product->id,
        'user_id' => $this->user->id,
        'is_active' => true,
    ]);
});

it('updates a model with array', function () {
    $this->action->handle($this->pivot, $this->input);

    $this->assertDatabaseHas('product_user', [
        'product_id' => $this->product->id,
        'user_id' => $this->user->id,
        'is_active' => true,
    ]);
});
