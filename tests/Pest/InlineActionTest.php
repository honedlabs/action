<?php

declare(strict_types=1);

use Honed\Action\ActionFactory;
use Honed\Action\InlineAction;
use Honed\Action\Tests\Fixtures\DestroyAction;
use Honed\Action\Tests\Stubs\Product;
use Symfony\Component\HttpFoundation\Request;

beforeEach(function () {
    $this->test = InlineAction::make('test');
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