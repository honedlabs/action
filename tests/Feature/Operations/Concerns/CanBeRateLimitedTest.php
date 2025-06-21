<?php

declare(strict_types=1);

use Honed\Action\Operations\InlineOperation;

beforeEach(function () {
    $this->operation = InlineOperation::make('test');
});

it('sets', function () {
    expect($this->operation)
        ->getRateLimit()->toBeNull()
        ->rateLimit(10)->toBe($this->operation)
        ->getRateLimit()->toBe(10);
});