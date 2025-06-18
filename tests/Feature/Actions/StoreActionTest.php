<?php

declare(strict_types=1);

use Illuminate\Support\ValidatedInput;
use Workbench\App\Actions\Product\StoreProduct;
use Workbench\App\Models\Product;

beforeEach(function () {
    $this->action = new StoreProduct();

    $this->assertDatabaseEmpty('products');
});

it('stores a model with ValidatedInput', function () {
    $input = new ValidatedInput([
        'name' => 'Test Product',
        'description' => 'A test product description',
        'price' => 100,
    ]);

    $product = $this->action->handle($input);

    $this->assertDatabaseCount('products', 1);

    $this->assertDatabaseHas('products', [
        'name' => 'Test Product',
        'description' => 'A test product description',
        'price' => 100,
    ]);

    $this->assertInstanceOf(Product::class, $product);
    $this->assertEquals('Test Product', $product->name);
    $this->assertEquals('A test product description', $product->description);
    $this->assertEquals(100, $product->price);
});

it('stores a model with form request', function () {
    $input = new ValidatedInput([
        'name' => 'Minimal Product',
    ]);

    $product = $this->action->handle($input);

    $this->assertDatabaseCount('products', 1);

    $this->assertDatabaseHas('products', [
        'name' => 'Minimal Product',
    ]);

    $this->assertInstanceOf(Product::class, $product);
    $this->assertEquals('Minimal Product', $product->name);
});
