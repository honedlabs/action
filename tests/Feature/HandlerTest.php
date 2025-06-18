<?php

declare(strict_types=1);

use Honed\Action\Handler;
use Honed\Action\Operations\InlineOperation;
use Workbench\App\Models\Product;

beforeEach(function () {
    $this->builder = Product::query();
    $this->handler = Handler::make($this->builder);
});

it('sets actions', function () {
    expect($this->handler)
        ->getActions()->toBeEmpty()
        ->actions([
            InlineOperation::make('update'),
        ])->getActions()->toHaveCount(1);
});

it('has key', function () {
    $key = $this->builder->getModel()->getKeyName();

    expect($this->handler)
        ->getKey($this->builder)->toBe($this->builder->qualifyColumn($key))
        ->key('public_id')->toBe($this->handler)
        ->getKey($this->builder)->toBe($this->builder->qualifyColumn('public_id'));
});

it('throws invalid argument exception', function () {
    $this->handler->throwInvalidActionTypeException('invalid');
})->throws(InvalidArgumentException::class);
