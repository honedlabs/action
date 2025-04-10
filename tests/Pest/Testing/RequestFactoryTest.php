<?php

declare(strict_types=1);

use Honed\Action\Testing\RequestFactory;
use Honed\Action\Testing\InlineRequest;
use Honed\Action\Testing\BulkRequest;
use Honed\Action\Testing\PageRequest;

beforeEach(function () {
    $this->factory = RequestFactory::make();
});

it('makes inline', function () {
    expect($this->factory)
        ->inline()->toBeInstanceOf(InlineRequest::class);
});

it('makes bulk', function () {
    expect($this->factory)
        ->bulk()->toBeInstanceOf(BulkRequest::class);
});

it('makes page', function () {
    expect($this->factory)
        ->page()->toBeInstanceOf(PageRequest::class);
});
