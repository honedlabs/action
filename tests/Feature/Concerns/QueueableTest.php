<?php

declare(strict_types=1);

use Honed\Action\Actions\Action;
use Honed\Action\Concerns\Queueable;
use Honed\Action\Contracts\Queueable as QueueableContract;
use Honed\Action\Jobs\QueuedAction;
use Illuminate\Foundation\Bus\PendingDispatch;
use Illuminate\Support\Facades\Queue;
use Workbench\App\Actions\Queue\AsynchronousAction;
use Workbench\App\Actions\TestQueueAction;

beforeEach(function () {
    $this->action = AsynchronousAction::make();

    Queue::fake();
});

it('has job', function () {
    expect($this->action)
        ->toJob()->toBeInstanceOf(QueuedAction::class)
        ->configureJob($job = $this->action->toJob())->toBe($job)
        ->queue()->toBeInstanceOf(QueuedAction::class);
});

it('dispatches job', function () {
    expect($this->action)
        ->dispatch()->toBeInstanceOf(PendingDispatch::class);

    Queue::assertPushed(QueuedAction::class);
});