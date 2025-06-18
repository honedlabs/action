<?php

declare(strict_types=1);

use Honed\Action\Operations\InlineOperation;

beforeEach(function () {
    $this->test = InlineOperation::make('test');
});

it('has inline type', function () {
    expect($this->test)
        ->getType()->toBe(InlineOperation::INLINE)
        ->isInline()->toBeTrue();
});

it('has default', function () {
    expect($this->test)
        ->isDefault()->toBeFalse()
        ->default()->toBe($this->test)
        ->isDefault()->toBeTrue();
});

it('has array representation', function () {
    expect($this->test->toArray())
        ->toBeArray()
        ->toHaveKey('default');
});
