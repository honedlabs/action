<?php

declare(strict_types=1);

use Honed\Action\ActionGroup;
use Honed\Action\Concerns\HasEndpoint;
use Honed\Action\Tests\Stubs\ProductActions;

beforeEach(function () {
    $this->test = new class()
    {
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

it('is executable', function () {
    $class = ActionGroup::class;

    expect($this->test)
        // Executes by default
        ->isExecutable()->toBeTrue()
        ->isExecutable($class)->toBeFalse()
        ->isntExecutable($class)->toBeTrue()
        ->executes()->toBe($this->test)
        ->isExecutable($class)->toBeFalse()
        ->isntExecutable($class)->toBeTrue();

    $actions = ProductActions::make();

    expect($actions)
        ->isExecutable(ActionGroup::class)->toBeTrue()
        ->isntExecutable(ActionGroup::class)->toBeFalse()
        ->executes(false)->toBe($actions)
        ->isExecutable(ActionGroup::class)->toBeFalse()
        ->isntExecutable(ActionGroup::class)->toBeTrue();
});

it('should be executable', function () {
    expect($this->test)
        ->shouldNotExecute()->toBe($this->test)
        ->isExecutable()->toBeFalse()
        ->shouldExecute()->toBe($this->test)
        ->isExecutable()->toBeTrue()
        ->shouldntExecute()->toBe($this->test)
        ->isExecutable()->toBeFalse();
});
