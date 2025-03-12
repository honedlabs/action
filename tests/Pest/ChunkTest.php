<?php

declare(strict_types=1);

use Honed\Action\BulkAction;
use Illuminate\Support\Collection;
use Honed\Action\Tests\Stubs\Product;

beforeEach(function () {
    $this->test = BulkAction::make('test');
});

it('chunks', function () {
    expect($this->test)
        ->isChunked()->toBe(config('action.chunk'))
        ->chunk()->toBe($this->test)
        ->isChunked()->toBeTrue();
});

it('chunks by id', function () {
    expect($this->test)
        ->chunksById()->toBe(config('action.chunk_by_id'))
        ->chunkById()->toBe($this->test)
        ->chunksById()->toBeTrue();
});

it('has chunk size', function () {
    expect($this->test)
        ->getChunkSize()->toBe(config('action.chunk_size'))
        ->chunkSize(100)->toBe($this->test)
        ->getChunkSize()->toBe(100);
});

