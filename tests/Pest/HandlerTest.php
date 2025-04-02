<?php

declare(strict_types=1);

use Honed\Action\Tests\Stubs\Product;
use Illuminate\Support\Arr;
use Honed\Action\ActionFactory;
use Illuminate\Support\Str;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\post;

beforeEach(function () {
    $this->id = Str::uuid()->toString();
});

describe('inline', function () {
    beforeEach(function () {
        $this->product = product();

        $this->base = [
            'id' => $this->id,
            'name' => 'update',
            'type' => ActionFactory::Inline,
            'record' => 1,
        ];
    });

    it('returns 400 for no name match', function () {
        Arr::set($this->base, 'name', 'missing');

        post(route('actions'), $this->base)
            ->assertStatus(400);
    });

    it('returns 404 if no model is found', function () {
        Arr::set($this->base, 'name', 'update.name');
        Arr::set($this->base, 'record', 0);

        post(route('actions'), $this->base)
            ->assertStatus(404);
    });

    it('returns 403 if the action is not allowed', function () {
        Arr::set($this->base, 'name', 'update.description');
        Arr::set($this->base, 'record', $this->product->id);

        post(route('actions'), $this->base)
            ->assertStatus(403);
    });

    it('executes', function () {
        Arr::set($this->base, 'name', 'update.name');
        Arr::set($this->base, 'record', $this->product->id);

        post(route('actions'), $this->base)
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

        $this->base = [
            'id' => $this->id,
            'name' => 'update',
            'type' => ActionFactory::Bulk,
            'all' => false,
            'except' => [],
            'only' => [],
        ];
    });

    it('returns 400 for no name match', function () {
        Arr::set($this->base, 'name', 'missing');

        post(route('actions'), $this->base)
            ->assertStatus(400);
    });

    it('returns 403 if the action is not allowed', function () {
        Arr::set($this->base, 'name', 'update.description');
        Arr::set($this->base, 'only', $this->ids);

        post(route('actions'), $this->base)
            ->assertStatus(403);
    });

    it('executes for only', function () {
        Arr::set($this->base, 'name', 'update.name');
        Arr::set($this->base, 'only', $this->ids);

        post(route('actions'), $this->base)
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
        Arr::set($this->base, 'name', 'update.name');
        Arr::set($this->base, 'except', $this->ids);

        post(route('actions'), $this->base)
            ->assertRedirect();

        // Will execute for all
        expect(Product::all())
            ->each(fn ($product) => $product
                ->name->not->toBe('test')
            );
    });

    it('executes for all with except', function () {
        Arr::set($this->base, 'name', 'update.name');
        Arr::set($this->base, 'all', true);
        Arr::set($this->base, 'except', $this->ids);

        post(route('actions'), $this->base)
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
        $this->base = [
            'id' => $this->id,
            'name' => 'update',
            'type' => ActionFactory::Page,
        ];
    });

    it('returns 400 for no name match', function () {
        Arr::set($this->base, 'name', 'missing');

        post(route('actions'), $this->base)
            ->assertStatus(400);

        assertDatabaseCount('products', 0);
    });

    it('returns 403 if the action is not allowed', function () {
        Arr::set($this->base, 'name', 'create.product.description');

        post(route('actions'), $this->base)
            ->assertStatus(403);

        assertDatabaseCount('products', 0);
    });

    it('executes', function () {
        Arr::set($this->base, 'name', 'create.product.name');

        post(route('actions'), $this->base)
            ->assertRedirect();

        assertDatabaseCount('products', 1);
    });
});