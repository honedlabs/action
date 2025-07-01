<?php

declare(strict_types=1);

use Honed\Action\Operations\BulkOperation;

beforeEach(function () {
    $this->action = BulkOperation::make('test');
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
        ->toHaveKey('keep');
});
