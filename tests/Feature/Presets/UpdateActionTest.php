<?php

declare(strict_types=1);

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Workbench\App\Actions\Product\UpdateProduct;
use Workbench\App\Http\Requests\NameRequest;
use Workbench\App\Models\Product;

beforeEach(function () {
    $this->action = new UpdateProduct();

    $this->product = Product::factory()->create();

    $this->name = fake()->unique()->name();

});

it('updates a model with validated input', function () {
    $input = Validator::make([
        'name' => $this->name,
    ], [
        'name' => 'required|string|max:255',
    ]);

    $this->action->handle($this->product, $input->safe());

    $this->assertDatabaseHas('products', [
        'id' => $this->product->id,
        'name' => $this->name,
    ]);
});

it('updates a model with a form request', function () {
    $request = Request::create('/', 'POST', [
        'name' => $this->name,
    ]);

    $this->app->instance('request', $request);
    
    $request = $this->app->make(NameRequest::class);

    $request->setContainer($this->app);

    $request->validateResolved();

    $this->action->handle($this->product, $request);

    $this->assertDatabaseHas('products', [
        'id' => $this->product->id,
        'name' => $this->name,
    ]);
});