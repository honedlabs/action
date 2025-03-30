<?php

declare(strict_types=1);

use Honed\Action\ActionGroup;
use Honed\Action\Concerns\HasEndpoint;

beforeEach(function () {
    $this->test = new class {
        use HasEndpoint;
    };
});

it('has endpoint', function () {
    expect($this->test)
        ->getEndpoint()->toBe(config('action.endpoint'))
        ->endpoint('/example')->toBe($this->test)
        ->getEndpoint()->toBe('/example')
        ->getDefaultEndpoint()->toBe(config('action.endpoint'));
});

// it('has server actions', function () {
//     expect($this->test)
//         ->hasServerActions(ActionGroup::class)->toBeTrue()
//         ->shouldExecute(false)->toBe($this->test)
//         ->hasServerActions(ActionGroup::class)->toBeFalse();
// });

