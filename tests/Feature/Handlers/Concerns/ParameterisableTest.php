<?php

declare(strict_types=1);

use Honed\Action\Handlers\BatchHandler;
use Workbench\App\Batches\UserBatch;

beforeEach(function () {
    $this->handler = BatchHandler::make(UserBatch::make());
});
