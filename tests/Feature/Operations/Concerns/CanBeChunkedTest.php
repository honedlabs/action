<?php

declare(strict_types=1);

use Honed\Action\Contracts\ShouldChunk;
use Honed\Action\Contracts\ShouldChunkById;
use Honed\Action\Operations\Concerns\CanBeChunked;

beforeEach(function () {
    $this->test = new class()
    {
        use CanBeChunked;
    };
});

it('chunks', function () {
    expect($this->test)
        ->isChunked()->toBeFalse()
        ->chunk()->toBe($this->test)
        ->isChunked()->toBeTrue();
});

it('chunks via contract', function () {
    $test = new class() implements ShouldChunk
    {
        use CanBeChunked;
    };

    expect($test)
        ->isChunked()->toBeTrue();
});

it('chunks by id', function () {
    expect($this->test)
        ->isChunkedById()->toBeFalse()
        ->chunkById()->toBe($this->test)
        ->isChunkedById()->toBeTrue();
});

it('chunks by id via contract', function () {
    $test = new class() implements ShouldChunkById
    {
        use CanBeChunked;
    };

    expect($test)
        ->isChunked()->toBeTrue()
        ->isChunkedById()->toBeTrue();
});

it('has chunk size', function () {
    expect($this->test)
        ->getChunkSize()->toBe(500)
        ->chunkSize(10)->toBe($this->test)
        ->getChunkSize()->toBe(10);
});
