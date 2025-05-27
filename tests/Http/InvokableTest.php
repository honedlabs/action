<?php

declare(strict_types=1);

use Honed\Action\Testing\InlineRequest;
use Honed\Action\Tests\Stubs\ProductActions;
use Honed\Action\Tests\Stubs\RouteProductActions;
use Honed\Action\Tests\Stubs\Product;

use function Pest\Laravel\post;

beforeEach(function () {
    $this->product = product();

    $this->actions = ProductActions::make();

    $this->request = InlineRequest::fake()
        ->for($this->actions)
        ->record($this->product->id)
        ->name('update.name')
        ->fill();
});

it('executes the action', function () {
    $data = $this->request->getData();

    $response = post(route('actions.invoke', $this->actions), $data);

    $response->assertRedirect();

    $this->assertDatabaseHas('products', [
        'id' => $this->product->id,
        'name' => 'test',
    ]);
});

it('does not execute non-existent action', function () {
    $key = ProductActions::encode(Product::class);

    $data = $this->request
        ->record($this->product->id)
        ->name('update.name')
        ->getData();

    $response = post(route('actions.invoke', $key), $data);

    $response->assertNotFound();
});

it('does not execute for non-executable actions', function () {
    $group = RouteProductActions::make();

    $data = $this->request->for($group)->getData();

    $response = post(route('actions.invoke', $group), $data);

    $response->assertNotFound();
});
