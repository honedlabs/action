<?php

declare(strict_types=1);

use Illuminate\Foundation\Http\FormRequest;
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

it('stores a model with minimal data', function () {
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

it('stores a model with all fillable attributes', function () {
    $input = new ValidatedInput([
        'name' => 'Complete Product',
        'description' => 'A complete product with all attributes',
        'price' => 149.99,
        'user_id' => 1,
    ]);

    $product = $this->action->handle($input);

    $this->assertDatabaseCount('products', 1);

    $this->assertDatabaseHas('products', [
        'name' => 'Complete Product',
        'description' => 'A complete product with all attributes',
        'price' => 149.99,
        'user_id' => 1,
    ]);

    $this->assertInstanceOf(Product::class, $product);
    $this->assertEquals('Complete Product', $product->name);
    $this->assertEquals('A complete product with all attributes', $product->description);
    $this->assertEquals(149.99, $product->price);
    $this->assertEquals(1, $product->user_id);
});

it('stores a model with FormRequest', function () {
    $formRequest = new class extends FormRequest {
        public function authorize(): bool
        {
            return true;
        }

        public function rules(): array
        {
            return [];
        }

        public function validationData(): array
        {
            return [
                'name' => 'FormRequest Product',
                'description' => 'Product created from FormRequest',
                'price' => 1100,
            ];
        }
    };

    // Mock the safe() method to return ValidatedInput
    $formRequest = Mockery::mock($formRequest);
    $formRequest->shouldReceive('safe')
        ->once()
        ->andReturn(new ValidatedInput([
            'name' => 'FormRequest Product',
            'description' => 'Product created from FormRequest',
            'price' => 1100,
        ]));

    $product = $this->action->handle($formRequest);

    $this->assertDatabaseCount('products', 1);

    $this->assertDatabaseHas('products', [
        'name' => 'FormRequest Product',
        'description' => 'Product created from FormRequest',
        'price' => 1100,
    ]);

    $this->assertInstanceOf(Product::class, $product);
    $this->assertEquals('FormRequest Product', $product->name);
});

it('stores multiple models sequentially', function () {
    $inputs = [
        new ValidatedInput([
            'name' => 'Product 1',
            'price' => 10.00,
        ]),
        new ValidatedInput([
            'name' => 'Product 2',
            'price' => 20.00,
        ]),
        new ValidatedInput([
            'name' => 'Product 3',
            'price' => 30.00,
        ]),
    ];

    $products = [];
    foreach ($inputs as $input) {
        $products[] = $this->action->handle($input);
    }

    $this->assertDatabaseCount('products', 3);

    $this->assertDatabaseHas('products', [
        'name' => 'Product 1',
        'price' => 10.00,
    ]);

    $this->assertDatabaseHas('products', [
        'name' => 'Product 2',
        'price' => 20.00,
    ]);

    $this->assertDatabaseHas('products', [
        'name' => 'Product 3',
        'price' => 30.00,
    ]);

    $this->assertCount(3, $products);
    foreach ($products as $product) {
        $this->assertInstanceOf(Product::class, $product);
    }
});

it('stores a model with empty optional fields', function () {
    $input = new ValidatedInput([
        'name' => 'Product with nulls',
        'description' => null,
        'price' => null,
    ]);

    $product = $this->action->handle($input);

    $this->assertDatabaseCount('products', 1);

    $this->assertDatabaseHas('products', [
        'name' => 'Product with nulls',
        'description' => null,
        'price' => null,
    ]);

    $this->assertInstanceOf(Product::class, $product);
    $this->assertEquals('Product with nulls', $product->name);
    $this->assertNull($product->description);
    $this->assertNull($product->price);
});
