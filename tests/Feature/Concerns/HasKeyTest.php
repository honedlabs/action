<?php

declare(strict_types=1);

use Workbench\App\Batches\UserBatch;

beforeEach(function () {
    $this->batch = UserBatch::make();
});

it('sets', function () {
    expect($this->batch)
        ->getKey()->toBeNull()
        ->key('test')->toBe($this->batch)
        ->getKey()->toBe('test');
});
