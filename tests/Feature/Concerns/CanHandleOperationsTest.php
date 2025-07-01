<?php

declare(strict_types=1);

use Honed\Action\Batch;
use Workbench\App\Batches\UserBatch;

beforeEach(function () {
    $this->batch = Batch::make();
});

it('finds primitive', function () {
    expect(Batch::find($this->batch->getRouteKey()))
        ->toBeNull();

    expect(Batch::find(UserBatch::make()->getRouteKey()))
        ->toBeInstanceOf(UserBatch::class);
});

it('resolves route binding', function () {
    expect($this->batch)
        ->resolveRouteBinding($this->batch->getRouteKey())
        ->toBeNull();

    expect(UserBatch::make())
        ->resolveRouteBinding(UserBatch::make()->getRouteKey())
        ->toBeInstanceOf(UserBatch::class);
});

it('has actionable array', function () {
    expect($this->batch->actionableToArray())
        ->toBeEmpty();

    expect(UserBatch::make()->actionableToArray())
        ->toBeArray()
        ->toHaveKeys([
            'id',
            'endpoint',
        ]);
});
