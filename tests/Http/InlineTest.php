<?php

declare(strict_types=1);

use Honed\Action\Testing\InlineRequest;
use Honed\Action\Tests\Stubs\ProductActions;
use Illuminate\Support\Str;

use function Pest\Laravel\post;

beforeEach(function () {
    $this->product = product();

    $this->request = InlineRequest::fake()
        ->for(ProductActions::class)
        ->fill();
});

it('executes the action', function () {
    $data = $this->request
        ->record($this->product->id)
        ->name('update.name')
        ->getData();

    $response = post(route('actions'), $data);

    $response->assertRedirect();

    $this->assertDatabaseHas('products', [
        'id' => $this->product->id,
        'name' => 'test',
    ]);
});

it('is 404 for no name match', function () {
    $data = $this->request
        ->record($this->product->id)
        ->name('missing')
        ->getData();

    $response = post(route('actions'), $data);

    $response->assertNotFound();
});

it('is 404 if no model is found', function () {
    $data = $this->request
        ->record(Str::uuid()->toString())
        ->name('update.name')
        ->getData();

    $response = post(route('actions'), $data);

    $response->assertNotFound();
});

it('is 404 if the action is not allowed', function () {
    // It's a 404 as the action when retrieved cannot be returned.
    $data = $this->request
        ->record($this->product->id)
        ->name('update.description')
        ->getData();

    $response = post(route('actions'), $data);

    $response->assertNotFound();
});

it('does not mix action types', function () {
    $data = $this->request
        ->record($this->product->id)
        ->name('create.product.name')
        ->getData();

    $response = post(route('actions'), $data);

    $response->assertNotFound();
});

it('does not execute route actions', function () {
    $data = $this->request
        ->record($this->product->id)
        ->name('show')
        ->getData();

    $response = post(route('actions'), $data);

    $response->assertRedirect(); // Returns back
});

it('returns inertia response', function () {
    $data = $this->request
        ->record($this->product->id)
        ->name('price.100')
        ->getData();

    $response = post(route('actions'), $data);

    $response->assertInertia();

    $this->assertDatabaseHas('products', [
        'id' => $this->product->id,
        'price' => 100,
    ]);
});
