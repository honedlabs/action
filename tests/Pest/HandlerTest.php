<?php

declare(strict_types=1);

use Honed\Action\Tests\RequestFactories\BulkActionRequest;
use Honed\Action\Tests\RequestFactories\InlineActionRequest;
use Honed\Action\Tests\RequestFactories\PageActionRequest;
use Honed\Action\Tests\Stubs\Product;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\post;

describe('inline', function () {
    beforeEach(function () {
        $this->product = product();

        $this->fn = fn (array $data = []) => InlineActionRequest::new($data)
            ->state($data)
            ->getFactoryData()
            ->getRequestedData();
    });

    it('returns 400 for no name match', function () {
        $data = \call_user_func($this->fn, ['name' => 'missing']);

        post(route('actions'), $data)
            ->assertStatus(400);
    });

    it('returns 404 if no model is found', function () {
        $data = \call_user_func($this->fn, ['name' => 'update.name', 'id' => 0]);

        post(route('actions'), $data)
            ->assertStatus(404);
    });

    it('returns 403 if the action is not allowed', function () {
        $data = \call_user_func($this->fn, ['name' => 'update.description', 'id' => $this->product->id]);

        post(route('actions'), $data)
            ->assertStatus(403);
    });

    it('executes', function () {
        $data = \call_user_func($this->fn, ['name' => 'update.name', 'id' => $this->product->id]);

        post(route('actions'), $data)
            ->assertRedirect();

        assertDatabaseHas('products', [
            'id' => $this->product->id,
            'name' => 'test',
        ]);
    });
});

describe('bulk', function () {
    beforeEach(function () {
        populate(100);

        $this->ids = \range(1, 10);

        $this->fn = fn (array $data = []) => BulkActionRequest::new($data)
            ->state($data)
            ->getFactoryData()
            ->getRequestedData();
    });

    it('returns 400 for no name match', function () {
        $data = \call_user_func($this->fn, ['name' => 'missing']);

        post(route('actions'), $data)
            ->assertStatus(400);
    });

    it('returns 403 if the action is not allowed', function () {
        $data = \call_user_func($this->fn, ['name' => 'update.description', 'only' => $this->ids]);

        post(route('actions'), $data)
            ->assertStatus(403);
    });

    it('executes for only', function () {
        $data = \call_user_func($this->fn, ['name' => 'update.name', 'only' => $this->ids]);

        post(route('actions'), $data)
            ->assertRedirect();

        expect(Product::query()
            ->whereIn('id', $this->ids)
            ->get())
            ->each(fn ($product) => $product
                ->name->toBe('test')
            );

        expect(Product::query()
            ->whereNotIn('id', $this->ids)
            ->get())
            ->each(fn ($product) => $product
                ->name->not->toBe('test')
            );
    });

    it('executes for except', function () {
        $data = \call_user_func($this->fn, ['name' => 'update.name', 'except' => $this->ids]);

        post(route('actions'), $data)
            ->assertRedirect();

        // Will execute for all
        expect(Product::all())
            ->each(fn ($product) => $product
                ->name->not->toBe('test')
            );
    });

    it('executes for all with except', function () {
        $data = \call_user_func($this->fn, ['name' => 'update.name', 'all' => true, 'except' => $this->ids]);

        post(route('actions'), $data)
            ->assertRedirect();

        expect(Product::query()
            ->whereNotIn('id', $this->ids)
            ->get())
            ->each(fn ($product) => $product
                ->name->toBe('test')
            );

        expect(Product::query()
            ->whereIn('id', $this->ids)
            ->get())
            ->each(fn ($product) => $product
                ->name->not->toBe('test')
            );
    });
});

describe('page', function () {
    beforeEach(function () {
        $this->fn = fn (array $data = []) => PageActionRequest::new($data)
            ->state($data)
            ->getFactoryData()
            ->getRequestedData();
    });

    it('returns 400 for no name match', function () {
        $data = \call_user_func($this->fn, ['name' => 'missing']);

        post(route('actions'), $data)
            ->assertStatus(400);

        // dd(Product::all());
    });

    it('returns 403 if the action is not allowed', function () {
        $data = \call_user_func($this->fn, ['name' => 'create.product.description']);

        post(route('actions'), $data)
            ->assertStatus(403);

        // assertDatabaseMissing('products', [
        //     'name' => 'description',
        //     'description' => 'description',
        // ]);
    });

    it('executes', function () {
        $data = \call_user_func($this->fn, ['name' => 'create.product.name']);

        post(route('actions'), $data)
            ->assertRedirect();

        // dd(Product::all());
    });
});
