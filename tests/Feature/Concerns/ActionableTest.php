<?php

declare(strict_types=1);

use Honed\Action\Batch;
use Workbench\App\Batches\UserBatch;

beforeEach(function () {
    $this->batch = Batch::make();
});

it('is actionable', function () {
    expect($this->batch)
        ->isActionable()->toBeFalse();

    $batch = UserBatch::make();

    expect($batch)
        ->isActionable()->toBeTrue()
        ->notActionable()->toBe($batch)
        ->isActionable()->toBeFalse();
});
