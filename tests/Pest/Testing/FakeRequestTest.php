<?php

declare(strict_types=1);

use Honed\Action\ActionFactory;
use Honed\Action\Http\Requests\InvokableRequest;
use Honed\Action\Testing\FakeRequest;
use Illuminate\Http\Request;

beforeEach(function () {
    FakeRequest::shouldFill(false);
    
    $this->request = new FakeRequest();
});

it('has id', function () {
    expect($this->request)
        ->getId()->toBeNull()
        ->id('123')->toBe($this->request)
        ->getId()->toBe('123');
});

it('has name', function () {
    expect($this->request)
        ->getName()->toBeNull()
        ->name('edit')->toBe($this->request)
        ->getName()->toBe('edit');
});

it('has uri', function () {
    expect($this->request)
        ->getUri()->toBe('/')
        ->uri('/test')->toBe($this->request)
        ->getUri()->toBe('/test');
});

it('fills', function () {
    expect($this->request)
        ->fills()->toBeFalse()
        ->fill()->toBe($this->request)
        ->fills()->toBeTrue();

    expect($this->request)
        ->getId()->toBeString()
        ->getName()->toBeString();
});

it('has data', function () {
    expect($this->request)
        ->getData()->scoped(fn ($data) => $data
            ->toBeArray()
            ->toHaveKeys(['id', 'name'])
        )
        ->data(['test' => 'test'])->toBe($this->request)
        ->getData()->scoped(fn ($data) => $data
            ->toBeArray()
            ->toHaveKeys(['id', 'name', 'test'])
        );
});

it('creates', function () {
    expect($this->request)
        ->create()->toBeInstanceOf(Request::class);
});

it('validates', function () {
    expect($this->request)
        ->fill()->toBe($this->request)
        ->data(['type' => ActionFactory::PAGE])->toBe($this->request)
        ->validate()->toBeInstanceOf(InvokableRequest::class);
});
