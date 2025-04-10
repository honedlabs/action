<?php

declare(strict_types=1);

use Honed\Action\ActionFactory;
use Honed\Action\Testing\BulkRequest;

beforeEach(function () {
    $this->request = new BulkRequest();
});

it('has all', function () {
    expect($this->request)
        ->isAll()->toBeFalse()
        ->all()->toBe($this->request)
        ->isAll()->toBeTrue();
});

it('has only', function () {
    expect($this->request)
        ->getOnly()->toBeEmpty()
        ->only([1, 2, 3])->toBe($this->request)
        ->getOnly()->toBe([1, 2, 3]);
});

it('has except', function () {
    expect($this->request)
        ->getExcept()->toBeEmpty()
        ->except([1, 2, 3])->toBe($this->request)
        ->getExcept()->toBe([1, 2, 3]);
});

it('has data', function () {
    expect($this->request)
        ->getData()->scoped(fn ($data) => $data
            ->toBeArray()
            ->toHaveKeys(['type', 'only', 'except', 'all', 'id', 'name'])
            ->{'type'}->toBe(ActionFactory::BULK)
        )
        ->data(['type' => 'test'])->toBe($this->request)
        ->getData()->scoped(fn ($data) => $data
            ->toBeArray()
            ->toHaveKeys(['type', 'only', 'except', 'all', 'id', 'name'])
            ->{'type'}->toBe('test')
        );
});
