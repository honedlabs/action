<?php

declare(strict_types=1);

use Honed\Action\Concerns\CanBeActionable;

beforeEach(function () {
    $this->test = new class()
    {
        use CanBeActionable;
    };
});

it('has endpoint', function () {
    expect($this->test)
        ->getEndpoint()->toBe(config('action.endpoint'))
        ->endpoint('/example')->toBe($this->test)
        ->getEndpoint()->toBe('/example')
        ->getDefaultEndpoint()->toBe(config('action.endpoint'));
});

it('is actionable', function () {
    expect($this->test)
        ->isActionable()->toBeTrue()
        ->isNotActionable()->toBeFalse()
        ->notActionable()->toBe($this->test)
        ->isActionable()->toBeFalse()
        ->isNotActionable()->toBeTrue()
        ->actionable()->toBe($this->test)
        ->isActionable()->toBeTrue()
        ->isNotActionable()->toBeFalse();
});
