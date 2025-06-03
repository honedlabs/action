<?php

declare(strict_types=1);

use Honed\Action\InlineAction;

beforeEach(function () {
    $this->test = InlineAction::make('test');
});

it('has inline type', function () {
    expect($this->test)
        ->getType()->toBe('inline')
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
