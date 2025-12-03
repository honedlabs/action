<?php

declare(strict_types=1);

use Honed\Action\Actions\Concerns\Transactable;

beforeEach(function () {
    $this->test = new class()
    {
        use Transactable;
    };
});

afterEach(function () {
    config()->set('action.transact', false);
});

it('can transact', function () {
    expect($this->test)
        ->isNotTransaction()->toBeTrue()
        ->isTransaction()->toBeFalse()
        ->transact()->toBe($this->test)
        ->isTransaction()->toBeTrue()
        ->dontTransact()->toBe($this->test)
        ->isNotTransaction()->toBeTrue();
});

it('calls transaction', function () {
    expect($this->test)
        ->isTransaction()->toBeFalse()
        ->transaction(fn () => 'test')->toBe('test')
        ->transact()->toBe($this->test)
        ->isTransaction()->toBeTrue()
        ->transaction(fn () => 'test')->toBe('test');
});