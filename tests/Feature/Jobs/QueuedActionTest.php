<?php

declare(strict_types=1);

use Honed\Action\Contracts\Action;
use Honed\Action\Exceptions\CannotQueueSynchronousActionException;
use Honed\Action\Jobs\QueuedAction;
use Illuminate\Support\Facades\Queue;
use Workbench\App\Actions\Queue\AsynchronousAction;
use Workbench\App\Actions\Queue\SynchronousAction;

use function Pest\Laravel\assertDatabaseCount;

beforeEach(function () {
    Queue::fake();
});

it('handles actions', function (string|Action $action) {
    $job = new QueuedAction($action);

    dispatch($job);

    Queue::assertPushed(QueuedAction::class);

    $job->withFakeQueueInteractions()->handle();

    assertDatabaseCount('users', 1);
})->with([
    [new AsynchronousAction()],
    [AsynchronousAction::class],
]);

it('checks synchronocity', function (string|Action $action, bool $error) {
    $job = new QueuedAction($action);

    dispatch($job);

    Queue::assertPushed(QueuedAction::class);

    $job->withFakeQueueInteractions()->handle();

    if ($error) {
        $job->assertFailedWith(CannotQueueSynchronousActionException::class);
    } else {
        $job->assertNotFailed();
    }
})->with([
    [SynchronousAction::class, true],
    [AsynchronousAction::class, false],
]);
