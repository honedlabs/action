<?php

declare(strict_types=1);

use Honed\Action\Testing\BulkRequest;
use Honed\Action\Tests\Fixtures\ProductActions;
use Honed\Action\Tests\Stubs\Product;

use function Pest\Laravel\post;

beforeEach(function () {
    foreach (range(1, 10) as $i) {
        product();
    }

    $this->request = BulkRequest::make()
        ->fill()
        ->for(ProductActions::class);
});

it('executes the action', function () {
    $data = $this->request
        ->all()
        ->name('update.description')
        ->getData();

    $response = post(route('actions'), $data);

    $response->assertRedirect();

    expect(Product::all())
        ->each(fn ($product) => $product
            ->description->toBe('test')
        );
});

it('is 404 for no name match', function () {
    $data = $this->request
        ->all()
        ->name('missing')
        ->getData();

    $response = post(route('actions'), $data);

    $response->assertNotFound();
});

it('is 404 if the action is not allowed', function () {
    // It's a 404 as the action when retrieved cannot be returned.
    $data = $this->request
        ->all()
        ->name('update.name')
        ->getData();

    $response = post(route('actions'), $data);

    $response->assertNotFound();
});

it('does not mix action types', function () {
    $data = $this->request
        ->all()
        ->name('create.product.name')
        ->getData();

    $response = post(route('actions'), $data);

    $response->assertNotFound();
});

it('returns inertia response', function () {
    $data = $this->request
        ->all()
        ->name('price.50')
        ->getData();

    $response = post(route('actions'), $data);

    $response->assertInertia();

    expect(Product::all())
        ->each(fn ($product) => $product
            ->price->toBe(50)
        );
});

it('applies only to selected records', function () {
    $ids = [1, 2, 3, 4, 5];
    $data = $this->request
        ->only($ids)
        ->name('update.description')
        ->getData();

    $response = post(route('actions'), $data);

    $response->assertRedirect();

    expect(Product::query()->whereIn('id', $ids)->get())
        ->each(fn ($product) => $product
            ->description->toBe('test')
        );

    expect(Product::query()->whereNotIn('id', $ids)->get())
        ->each(fn ($product) => $product
            ->description->not->toBe('test')
        );

});

it('applies all excepted records', function () {
    $ids = [1, 2];

    $data = $this->request
        ->all()
        ->except($ids)
        ->name('update.description')
        ->getData();

    $response = post(route('actions'), $data);

    $response->assertRedirect();

    expect(Product::query()->whereIn('id', $ids)->get())
        ->each(fn ($product) => $product
            ->description->not->toBe('test')
        );

    expect(Product::query()->whereNotIn('id', $ids)->get())
        ->each(fn ($product) => $product
            ->description->toBe('test')
        );
});
