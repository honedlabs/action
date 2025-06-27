<?php

declare(strict_types=1);

use Honed\Action\Operations\InlineOperation;

beforeEach(function () {
    $this->operation = InlineOperation::make('test');
});

it('can be inertia', function () {
    expect($this->operation)
        ->isInertia()->toBeTrue()
        ->inertia()->toBe($this->operation)
        ->isInertia()->toBeTrue()
        ->toArray()
        ->scoped(fn ($array) => $array
            ->toHaveKey('inertia')
            ->{'inertia'}->toBeTrue()
        );
});

it('can be not inertia', function () {
    expect($this->operation)
        ->isNotInertia()->toBeFalse()
        ->notInertia()->toBe($this->operation)
        ->isNotInertia()->toBeTrue()
        ->toArray()
        ->scoped(fn ($array) => $array
            ->not->toHaveKey('inertia')
        );
});
