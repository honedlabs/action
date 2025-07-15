<?php

declare(strict_types=1);

use Honed\Action\Concerns\CanChunk;
use Honed\Action\Contracts\ShouldChunk;
use Honed\Action\Contracts\ShouldChunkById;
use Honed\Action\Operations\BulkOperation;

beforeEach(function () {
    $this->operation = BulkOperation::make('test');
});

it('chunks', function () {
    expect($this->operation)
        ->isChunked()->toBeFalse()
        ->isNotChunked()->toBeTrue()
        ->chunk()->toBe($this->operation)
        ->isChunked()->toBeTrue()
        ->dontChunk()->toBe($this->operation)
        ->isNotChunked()->toBeTrue();
});

it('chunks via contract', function () {
    $test = new class() implements ShouldChunk
    {
        use CanChunk;
    };

    expect($test)
        ->isChunked()->toBeTrue()
        ->isNotChunked()->toBeFalse();
});

it('chunks by id', function () {
    expect($this->operation)
        ->isChunkedById()->toBeFalse()
        ->isNotChunkedById()->toBeTrue()
        ->chunkById()->toBe($this->operation)
        ->isChunkedById()->toBeTrue()
        ->dontChunkById()->toBe($this->operation)
        ->isNotChunkedById()->toBeTrue();
});

it('chunks by id via contract', function () {
    $test = new class() implements ShouldChunkById
    {
        use CanChunk;
    };

    expect($test)
        ->isChunked()->toBeTrue()
        ->isChunkedById()->toBeTrue();
});

it('has chunk size', function () {
    expect($this->operation)
        ->getChunkSize()->toBe(BulkOperation::CHUNK_SIZE)
        ->chunkSize(10)->toBe($this->operation)
        ->getChunkSize()->toBe(10);
});
