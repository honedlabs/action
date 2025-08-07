<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Workbench\App\Actions\Product\UpdateUser;
use Workbench\App\Http\Requests\NameRequest;
use Workbench\App\Models\Product;
use Workbench\App\Models\User;

beforeEach(function () {
    $this->action = new UpdateUser();

    $this->user = User::factory()->create();

    $this->product = Product::factory()->for($this->user)->create();

    $this->input = [
        'name' => 'Updated',
    ];
});

it('updates pivot with validated input', function () {
    $input = Validator::make($this->input, [
        'name' => 'required|string|max:255',
    ]);

    $this->action->handle($this->product, $input->safe());

    $this->assertDatabaseHas('users', [
        'id' => $this->user->id,
        'name' => $this->input['name'],
    ]);
});

it('updates a model with a form request', function () {
    $request = Request::create('/', 'POST', $this->input);

    $this->app->instance('request', $request);

    $request = $this->app->make(NameRequest::class);

    $request->setContainer($this->app);

    $request->validateResolved();

    $this->action->handle($this->product, $request);

    $this->assertDatabaseHas('users', [
        'id' => $this->user->id,
        'name' => $this->input['name'],
    ]);
});

it('updates a model with array', function () {
    $this->action->handle($this->product, $this->input);

    $this->assertDatabaseHas('users', [
        'id' => $this->user->id,
        'name' => $this->input['name'],
    ]);
});
