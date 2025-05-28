<?php

declare(strict_types=1);

use Honed\Action\Support\Constants;
use Honed\Action\Testing\InlineRequest;

beforeEach(function () {
    $this->request = new InlineRequest;
});

it('has record', function () {
    expect($this->request)
        ->getRecord()->toBeNull()
        ->record(1)->toBe($this->request)
        ->getRecord()->toBe(1);
});

it('has data', function () {
    expect($this->request)
        ->getData()->scoped(fn ($data) => $data
        ->toBeArray()
        ->toHaveKeys(['type', 'record', 'id', 'name'])
        ->{'type'}->toBe(Constants::INLINE)
        )
        ->data(['type' => 'test'])->toBe($this->request)
        ->getData()->scoped(fn ($data) => $data
        ->toBeArray()
        ->toHaveKeys(['type', 'record', 'id', 'name'])
        ->{'type'}->toBe('test')
        );
});
