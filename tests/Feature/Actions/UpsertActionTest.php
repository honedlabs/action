<?php

declare(strict_types=1);

use Illuminate\Support\ValidatedInput;
use Workbench\App\Actions\Product\UpsertProduct;
use Workbench\App\Models\Product;

beforeEach(function () {
    $this->action = new UpsertProduct();

    $this->name = fake()->unique()->name();
});

it('upserts a single record from array', function () {
    $product = Product::factory()->create();

    $values = Product::factory()
        ->create()
        ->getAttributes();

    $this->action->handle([$values]);

    $this->assertDatabaseCount('products', 2);
});

it('upserts multiple records from array', function () {
    $this->assertDatabaseEmpty('products');

    $values = [
        [
            'name' => 'Product One',
            'description' => 'Description One',
            'price' => 19.99,
        ],
        [
            'name' => 'Product Two',
            'description' => 'Description Two',
            'price' => 29.99,
        ],
        [
            'name' => 'Product Three',
            'description' => 'Description Three',
            'price' => 39.99,
        ],
    ];

    $this->action->handle($values);

    $this->assertDatabaseCount('products', 3);

    $this->assertDatabaseHas('products', [
        'name' => 'Product One',
        'description' => 'Description One',
        'price' => 19.99,
    ]);

    $this->assertDatabaseHas('products', [
        'name' => 'Product Two',
        'description' => 'Description Two',
        'price' => 29.99,
    ]);

    $this->assertDatabaseHas('products', [
        'name' => 'Product Three',
        'description' => 'Description Three',
        'price' => 39.99,
    ]);
});

it('updates existing record when unique constraint matches', function () {
    $this->assertDatabaseEmpty('products');

    // First, create a product
    Product::factory()->create([
        'name' => 'Existing Product',
        'description' => 'Original Description',
        'price' => 50.00,
    ]);

    $this->assertDatabaseCount('products', 1);

    // Now upsert with the same name but different updateable fields
    $values = [
        [
            'name' => 'Existing Product',
            'description' => 'Updated Description',
            'price' => 75.00,
        ],
    ];

    $this->action->handle($values);

    // Should still be only 1 record
    $this->assertDatabaseCount('products', 1);

    // But with updated values
    $this->assertDatabaseHas('products', [
        'name' => 'Existing Product',
        'description' => 'Updated Description',
        'price' => 75.00,
    ]);
});

it('handles mix of new and existing records', function () {
    $this->assertDatabaseEmpty('products');

    // Create an existing product
    Product::factory()->create([
        'name' => 'Existing Product',
        'description' => 'Original Description',
        'price' => 50.00,
    ]);

    $values = [
        [
            'name' => 'Existing Product',
            'description' => 'Updated Description',
            'price' => 75.00,
        ],
        [
            'name' => 'New Product',
            'description' => 'New Description',
            'price' => 25.00,
        ],
    ];

    $this->action->handle($values);

    $this->assertDatabaseCount('products', 2);

    $this->assertDatabaseHas('products', [
        'name' => 'Existing Product',
        'description' => 'Updated Description',
        'price' => 75.00,
    ]);

    $this->assertDatabaseHas('products', [
        'name' => 'New Product',
        'description' => 'New Description',
        'price' => 25.00,
    ]);
});

it('upserts from ValidatedInput', function () {
    $this->assertDatabaseEmpty('products');

    $data = [
        'name' => 'Validated Product',
        'description' => 'Validated Description',
        'price' => 89.99,
    ];

    $validatedInput = new ValidatedInput($data);

    $this->action->handle($validatedInput);

    $this->assertDatabaseCount('products', 1);

    $this->assertDatabaseHas('products', [
        'name' => 'Validated Product',
        'description' => 'Validated Description',
        'price' => 89.99,
    ]);
});

it('updates existing record from ValidatedInput', function () {
    $this->assertDatabaseEmpty('products');

    // Create existing product
    Product::factory()->create([
        'name' => 'Validated Product',
        'description' => 'Original Description',
        'price' => 50.00,
    ]);

    $data = [
        'name' => 'Validated Product',
        'description' => 'Updated via Validation',
        'price' => 99.99,
    ];

    $validatedInput = new ValidatedInput($data);

    $this->action->handle($validatedInput);

    $this->assertDatabaseCount('products', 1);

    $this->assertDatabaseHas('products', [
        'name' => 'Validated Product',
        'description' => 'Updated via Validation',
        'price' => 99.99,
    ]);
});

it('upserts from FormRequest', function () {
    $this->assertDatabaseEmpty('products');

    $request = new class() extends Illuminate\Foundation\Http\FormRequest
    {
        public function authorize(): bool
        {
            return true;
        }

        public function rules(): array
        {
            return [
                'name' => 'required|string',
                'description' => 'required|string',
                'price' => 'required|numeric',
            ];
        }

        public function safe(?array $keys = null): ValidatedInput
        {
            return new ValidatedInput([
                'name' => 'Form Request Product',
                'description' => 'Form Request Description',
                'price' => 199.99,
            ]);
        }
    };

    $this->action->handle($request);

    $this->assertDatabaseCount('products', 1);

    $this->assertDatabaseHas('products', [
        'name' => 'Form Request Product',
        'description' => 'Form Request Description',
        'price' => 199.99,
    ]);
});

it('updates existing record from FormRequest', function () {
    $this->assertDatabaseEmpty('products');

    // Create existing product
    Product::factory()->create([
        'name' => 'Form Request Product',
        'description' => 'Original Description',
        'price' => 50.00,
    ]);

    $request = new class() extends Illuminate\Foundation\Http\FormRequest
    {
        public function authorize(): bool
        {
            return true;
        }

        public function rules(): array
        {
            return [
                'name' => 'required|string',
                'description' => 'required|string',
                'price' => 'required|numeric',
            ];
        }

        public function safe(?array $keys = null): ValidatedInput
        {
            return new ValidatedInput([
                'name' => 'Form Request Product',
                'description' => 'Updated via Form Request',
                'price' => 299.99,
            ]);
        }
    };

    $this->action->handle($request);

    $this->assertDatabaseCount('products', 1);

    $this->assertDatabaseHas('products', [
        'name' => 'Form Request Product',
        'description' => 'Updated via Form Request',
        'price' => 299.99,
    ]);
});
