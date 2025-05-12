<?php

declare(strict_types=1);

namespace Tests\Pest\Handler;

use Honed\Action\Testing\PageRequest;
use Honed\Action\Tests\Stubs\ProductActions;
use Honed\Action\Tests\Stubs\Product;

use function Pest\Laravel\post;

beforeEach(function () {
    $this->request = PageRequest::fake()
        ->for(ProductActions::class)
        ->fill();
});

it('executes the action', function () {
    $data = $this->request
        ->name('create.product.name')
        ->getData();

    $response = post(route('actions'), $data);

    $response->assertRedirect();

    $this->assertDatabaseHas('products', [
        'name' => 'name',
        'description' => 'name',
    ]);
});

it('is 404 for no name match', function () {
    $data = $this->request
        ->name('missing')
        ->getData();

    $response = post(route('actions'), $data);

    $response->assertNotFound();
});

it('is 404 if the action is not allowed', function () {
    // It's a 404 as the action when retrieved cannot be returned.
    $data = $this->request
        ->name('create.product.description')
        ->getData();

    $response = post(route('actions'), $data);

    $response->assertNotFound();
});

it('does not execute route actions', function () {
    $data = $this->request
        ->name('create')
        ->getData();

    $response = post(route('actions'), $data);

    $response->assertRedirect();
});

it('returns inertia response', function () {
    product();
    $data = $this->request
        ->name('price.10')
        ->getData();

    $response = post(route('actions'), $data);

    $response->assertInertia();

    expect(Product::all())
        ->toHaveCount(1)
        ->first()->price->toBe(10);
});
