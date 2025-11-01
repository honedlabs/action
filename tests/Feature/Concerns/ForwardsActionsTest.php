<?php

declare(strict_types=1);

use Honed\Action\Actions\UpdateAction;
use Honed\Action\Concerns\ForwardsActions;
use Honed\Action\Contracts\Action;
use Workbench\App\Models\Product;

beforeEach(function () {
    $this->class = new class() implements Action
    {
        use ForwardsActions;

        public function handle(Product $product, array $input = [])
        {
            return $this->forward($product, $input);
        }

        public function action(): string
        {
            return UpdateAction::class;
        }
    };
});

it('forwards action', function () {
    $product = Product::factory()->create();

    $input = [
        'name' => 'Test',
    ];

    expect($this->class)
        ->action()->toBe(UpdateAction::class)
        ->getAction()->toBeInstanceOf(UpdateAction::class)
        ->handle($product, $input)->toBeInstanceOf(Product::class);

    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'name' => 'Test',
    ]);
});
