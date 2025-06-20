<?php

declare(strict_types=1);

use Honed\Action\Concerns\CanBeTransaction;

beforeEach(function () {
    $this->test = new class()
    {
        use CanBeTransaction;
    };
});

afterEach(function () {
    $this->test->shouldBeTransaction(false);
});

it('can be a transaction', function () {
    expect($this->test)
        ->isTransaction()->toBeFalse()
        ->transaction()->toBe($this->test)
        ->isTransaction()->toBeTrue();
});

it('configures to be a transaction', function () {
    $this->test->shouldBeTransaction(true);

    expect($this->test)
        ->isTransaction()->toBeTrue();

});
