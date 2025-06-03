<?php

declare(strict_types=1);

use Honed\Action\BulkAction;

beforeEach(function () {
    $this->action = BulkAction::make('test');
});

it('has bulk type', function () {
    expect($this->action)
        ->getType()->toBe('bulk')
        ->isBulk()->toBeTrue();
});

it('keeps selected', function () {
    expect($this->action)
        ->keepsSelected()->toBeFalse()
        ->keepSelected()->toBe($this->action)
        ->keepsSelected()->toBeTrue();
});

it('has array representation', function () {
    expect($this->action->toArray())
        ->toBeArray()
        ->toHaveKey('keepSelected');
});
