<?php

declare(strict_types=1);

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
