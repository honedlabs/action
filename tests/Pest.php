<?php

declare(strict_types=1);

use Honed\Action\BulkAction;
use Honed\Action\InlineAction;
use Honed\Action\PageAction;
use Honed\Action\Tests\Stubs\Product;
use Honed\Action\Tests\Stubs\Status;
use Honed\Action\Tests\TestCase;
use Illuminate\Support\Str;

uses(TestCase::class)->in(__DIR__);

function product(?string $name = null): Product
{
    return Product::create([
        'public_id' => Str::uuid(),
        'name' => $name ?? fake()->unique()->word(),
        'description' => fake()->sentence(),
        'price' => fake()->randomNumber(4),
        'best_seller' => fake()->boolean(),
        'status' => fake()->randomElement(Status::cases()),
        'created_at' => now()->subDays(fake()->randomNumber(2)),
    ]);
}

function populate(int $count = 1000): void
{
    foreach (\range(1, $count) as $i) {
        product();
    }
}

/**
 * @return array{array<string,mixed>,array<string,mixed>}
 */
function params(Product $product): array
{
    $named = [
        'product' => $product,
    ];

    $typed = [
        Product::class => $product,
    ];

    return [$named, $typed];
}

/**
 * Get the testing actions.
 *
 * @return \Illuminate\Support\Collection<int,\Honed\Action\Action>
 */
function actions()
{
    return collect([
        InlineAction::make('update.name')
            ->action(fn ($product) => $product->update(['name' => 'test'])),

        InlineAction::make('update.description')
            ->action(fn ($product) => $product->update(['description' => 'test']))
            ->allow(false),

        BulkAction::make('update.name')
            ->action(fn ($product) => $product->update(['name' => 'test'])),

        BulkAction::make('update.description')
            ->action(fn ($product) => $product->update(['description' => 'test']))
            ->allow(false),

        PageAction::make('create.product.name')
            ->action(fn () => $this->createProduct('name', 'name')),

        PageAction::make('create.product.description')
            ->action(fn () => $this->createProduct('description', 'description'))
            ->allow(false),
    ]);
}
