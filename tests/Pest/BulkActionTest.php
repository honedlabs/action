<?php

declare(strict_types=1);

use Honed\Action\BulkAction;
use Honed\Action\ActionFactory;
use Honed\Action\Tests\Stubs\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Pest\Expectation;

beforeEach(function () {
    $this->action = BulkAction::make('test');

    foreach (range(1, 10) as $i) {
        product();
    }
});

it('makes', function () {
    expect($this->action)
        ->toBeInstanceOf(BulkAction::class);
});

it('has array representation', function () {
    expect($this->action->toArray())
        ->toBeArray()
        ->toEqual([
            'name' => 'test',
            'label' => 'Test',
            'type' => ActionFactory::Bulk,
            'icon' => null,
            'extra' => [],
            'action' => false,
            'confirm' => null,
            'action' => false,
            'keepSelected' => false,
            'route' => null,
        ]);
});

it('keeps selected', function () {
    expect($this->action)
        ->keepsSelected()->toBeFalse()
        ->keepSelected()->toBe($this->action)
        ->keepsSelected()->toBeTrue();
});

it('executes', function () {
    $product = product();

    $fn = fn (Builder $q) => $q->update(['name' => 'test']);

    $this->action->action($fn)->execute(Product::query());
    
    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'name' => 'test',
    ]); 
});