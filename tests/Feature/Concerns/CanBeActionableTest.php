<?php

declare(strict_types=1);

use Honed\Action\Concerns\Actionable;

beforeEach(function () {
    $this->test = new class()
    {
        use Actionable;
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
        ->actionable(false)->toBe($this->test)
        ->isActionable()->toBeFalse();
});
