<?php

declare(strict_types=1);

use Honed\Action\ActionGroup;
use Honed\Action\Concerns\HasEndpoint;
use Honed\Action\Tests\Fixtures\ProductActions;

beforeEach(function () {
    $this->test = new class {
        use HasEndpoint;

        public function handle($request)
        {
            return $request;
        }
    };
});

it('has endpoint', function () {
    expect($this->test)
        ->getEndpoint()->toBe(config('action.endpoint'))
        ->endpoint('/example')->toBe($this->test)
        ->getEndpoint()->toBe('/example')
        ->getDefaultEndpoint()->toBe(config('action.endpoint'));
});

it('can execute server actions', function () {
    $class = ActionGroup::class;

    expect($this->test)
        ->isExecutable($class)->toBeFalse()
        ->isNotExecutable($class)->toBeTrue()
        ->shouldExecute()->toBe($this->test)
        ->isExecutable($class)->toBeFalse()
        ->isNotExecutable($class)->toBeTrue();

    $actions = ProductActions::make();
    
    expect($actions)
        ->isExecutable(ActionGroup::class)->toBeTrue()
        ->isNotExecutable(ActionGroup::class)->toBeFalse()
        ->shouldNotExecute()->toBe($actions)
        ->isExecutable(ActionGroup::class)->toBeFalse()
        ->isNotExecutable(ActionGroup::class)->toBeTrue();
});
