<?php

declare(strict_types=1);

use Honed\Action\Batch;
use Honed\Action\Concerns\HasEndpoint;
use Workbench\App\Batches\UserBatch;

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
    $class = Batch::class;

    expect($this->test)
        // Executes by default
        ->isExecutable()->toBeTrue()
        ->isExecutable($class)->toBeFalse()
        ->isNotExecutable($class)->toBeTrue()
        ->executes()->toBe($this->test)
        ->isExecutable($class)->toBeFalse()
        ->isNotExecutable($class)->toBeTrue();

    $actions = UserBatch::make();
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
