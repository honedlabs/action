<?php

declare(strict_types=1);

use Workbench\App\Actions\Product\ReplicateProduct;
use Workbench\App\Models\Product;

beforeEach(function () {
    $this->action = new ReplicateProduct();

    $this->product = Product::factory()->create([
        'name' => 'Original Product',
        'description' => 'Original Description',
        'price' => 100,
    ]);
});

it('replicates a model without attributes', function () {
    $initialCount = Product::count();

    $replicated = $this->action->handle($this->product);

    expect(Product::count())->toBe($initialCount + 1);
    expect($replicated)->toBeInstanceOf(Product::class);
    expect($replicated->id)->not->toBe($this->product->id);
    expect($replicated->name)->toBe($this->product->name);
    expect($replicated->description)->toBe($this->product->description);
    expect($replicated->price)->toBe($this->product->price);
});

it('replicates a model with array attributes', function () {
    $initialCount = Product::count();

    $attributes = [
        'name' => 'Replicated Product',
        'price' => 150,
    ];

    $replicated = $this->action->handle($this->product, $attributes);

    expect(Product::count())->toBe($initialCount + 1);
    expect($replicated)->toBeInstanceOf(Product::class);
    expect($replicated->id)->not->toBe($this->product->id);
    expect($replicated->name)->toBe('Replicated Product');
    expect($replicated->description)->toBe($this->product->description);
    expect($replicated->price)->toBe(150);
});

it('replicates a model with empty array attributes', function () {
    $initialCount = Product::count();

    $replicated = $this->action->handle($this->product, []);

    expect(Product::count())->toBe($initialCount + 1);
    expect($replicated)->toBeInstanceOf(Product::class);
    expect($replicated->id)->not->toBe($this->product->id);
    expect($replicated->name)->toBe($this->product->name);
    expect($replicated->description)->toBe($this->product->description);
    expect($replicated->price)->toBe($this->product->price);
});

it('replicates a model with partial attributes', function () {
    $initialCount = Product::count();

    $attributes = [
        'name' => 'Partially Updated Product',
    ];

    $replicated = $this->action->handle($this->product, $attributes);

    expect(Product::count())->toBe($initialCount + 1);
    expect($replicated)->toBeInstanceOf(Product::class);
    expect($replicated->id)->not->toBe($this->product->id);
    expect($replicated->name)->toBe('Partially Updated Product');
    expect($replicated->description)->toBe($this->product->description);
    expect($replicated->price)->toBe($this->product->price);
});

it('replicates a model with all attributes overridden', function () {
    $initialCount = Product::count();

    $attributes = [
        'name' => 'Completely New Product',
        'description' => 'Completely New Description',
        'price' => 200,
    ];

    $replicated = $this->action->handle($this->product, $attributes);

    expect(Product::count())->toBe($initialCount + 1);
    expect($replicated)->toBeInstanceOf(Product::class);
    expect($replicated->id)->not->toBe($this->product->id);
    expect($replicated->name)->toBe('Completely New Product');
    expect($replicated->description)->toBe('Completely New Description');
    expect($replicated->price)->toBe(200);
});

it('preserves timestamps behavior during replication', function () {
    $initialCount = Product::count();

    $replicated = $this->action->handle($this->product);

    expect(Product::count())->toBe($initialCount + 1);
    expect($replicated->created_at)->not->toBe($this->product->created_at);
    expect($replicated->updated_at)->not->toBe($this->product->updated_at);
});

it('replicates with null attribute values', function () {
    $initialCount = Product::count();

    $attributes = [
        'description' => null,
    ];

    $replicated = $this->action->handle($this->product, $attributes);

    expect(Product::count())->toBe($initialCount + 1);
    expect($replicated)->toBeInstanceOf(Product::class);
    expect($replicated->id)->not->toBe($this->product->id);
    expect($replicated->name)->toBe($this->product->name);
    expect($replicated->description)->toBeNull();
    expect($replicated->price)->toBe($this->product->price);
});
