<?php

declare(strict_types=1);

use Honed\Action\ActionGroup;
use Honed\Action\Concerns\HasEndpoint;
use Honed\Action\Tests\Fixtures\ProductActions;

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

it('can execute server actions', function () {
    $class = ActionGroup::class;

    expect($this->test)
        ->canExecuteServerActions($class)->toBeFalse()
        ->cannotExecuteServerActions($class)->toBeTrue()
        ->shouldExecute()->toBe($this->test)
        ->canExecuteServerActions($class)->toBeFalse()
        ->cannotExecuteServerActions($class)->toBeTrue();

    $actions = ProductActions::make();
    
    expect($actions)
        ->canExecuteServerActions(ActionGroup::class)->toBeTrue()
        ->cannotExecuteServerActions(ActionGroup::class)->toBeFalse()
        ->shouldNotExecute()->toBe($actions)
        ->canExecuteServerActions(ActionGroup::class)->toBeFalse()
        ->cannotExecuteServerActions(ActionGroup::class)->toBeTrue();
});
