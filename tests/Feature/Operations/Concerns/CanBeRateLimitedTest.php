<?php

declare(strict_types=1);

use Honed\Action\Operations\InlineOperation;

beforeEach(function () {
    $this->operation = InlineOperation::make('test');
});

it('sets number of attempts', function () {
    expect($this->operation)
        ->getRateLimit()->toBeNull()
        ->rateLimit(10)->toBe($this->operation)
        ->getRateLimit()->toBe(10)
        ->dontRateLimit()->toBe($this->operation)
        ->getRateLimit()->toBeNull();
});

it('sets rate limit key', function () {
    expect($this->operation)
        ->getRateLimitBy()->toBeNull()
        ->rateLimitBy('test')->toBe($this->operation)
        ->getRateLimitBy()->toBe('test');
});
