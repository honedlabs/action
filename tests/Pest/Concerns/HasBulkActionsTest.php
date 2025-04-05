<?php

declare(strict_types=1);

use Honed\Action\Concerns\HasBulkActions;
use Honed\Action\Contracts\ShouldChunk;

beforeEach(function () {
    $this->test = new class {
        use HasBulkActions;
    };
});

it('chunks', function () {
    expect($this->test)
        ->isChunked()->toBe(config('action.chunk'))
        ->chunk(true)->toBe($this->test)
        ->isChunked()->toBeTrue()
        ->isChunkedByDefault()->toBe(config('action.chunk'));

    $test = new class implements ShouldChunk {
        use HasBulkActions;
    };

    expect($test)
        ->isChunked()->toBeTrue();
});

it('chunks by id', function () {
    expect($this->test)
        ->chunksById()->toBe(config('action.chunk_by_id'))
        ->chunkById(true)->toBe($this->test)
        ->chunksById()->toBeTrue()
        ->chunksByIdByDefault()->toBe(config('action.chunk_by_id'));
});

it('has chunk size', function () {
    expect($this->test)
        ->getChunkSize()->toBe(config('action.chunk_size'))
        ->chunkSize(10)->toBe($this->test)
        ->getChunkSize()->toBe(10)
        ->getDefaultChunkSize()->toBe(config('action.chunk_size'));
});


