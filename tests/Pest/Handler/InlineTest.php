<?php

declare(strict_types=1);

use Illuminate\Support\Str;
use function Pest\Laravel\post;
use Honed\Action\Http\Requests\InvokableRequest;

beforeEach(function () {
    $this->product = product();
});

it('executes the action', function () {
    $data = InvokableRequest::fake()
        ->inline()
        ->fill()
        ->record($this->product->id)
        ->name('update.name')
        ->getData();

    $response = post(route('actions'), $data);
    
    $response->assertRedirect();
});

it('is 404 for no name match', function () {
    $data = InvokableRequest::fake()
        ->inline()
        ->fill()
        ->record($this->product->id)
        ->name('missing')
        ->getData();

    $response = post(route('actions'), $data);

    $response->assertNotFound();
}); 

it('is 404 if no model is found', function () {
    $data = InvokableRequest::fake()
        ->inline()
        ->fill()
        ->record(Str::uuid()->toString())
        ->name('update.name')
        ->getData();

    $response = post(route('actions'), $data);

    $response->assertNotFound();
});

it('is 403 if the action is not allowed', function () {
    $data = InvokableRequest::fake()
        ->inline()
        ->fill()
        ->record($this->product->id)
        ->name('update.description')
        ->getData();

    $response = post(route('actions'), $data);

    $response->assertForbidden();
});

it('does not mix action types', function () {
    $data = InvokableRequest::fake()
        ->inline()
        ->fill()
        ->record($this->product->id)
        ->name('create.product.name')
        ->getData();

    $response = post(route('actions'), $data);

    $response->assertNotFound();
});

it('does not execute route actions', function () {
    $data = InvokableRequest::fake()
        ->inline()
        ->fill()
        ->record($this->product->id)
        ->name('show')
        ->getData();

    $response = post(route('actions'), $data);

    $response->assertRedirect(); // Returns back
});

it('returns inertia response', function () {
    $data = InvokableRequest::fake()
        ->inline()
        ->fill()
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
