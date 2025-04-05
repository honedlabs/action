<?php

declare(strict_types=1);

use Honed\Action\Testing\RequestFactory;
use Honed\Action\Testing\InlineActionRequest;
use Honed\Action\Testing\BulkActionRequest;
use Honed\Action\Testing\PageActionRequest;

beforeEach(function () {
    $this->factory = RequestFactory::make();
});

it('makes inline', function () {
    expect($this->factory)
        ->inline()->toBeInstanceOf(InlineActionRequest::class);
});

it('makes bulk', function () {
    expect($this->factory)
        ->bulk()->toBeInstanceOf(BulkActionRequest::class);
});

it('makes page', function () {
    expect($this->factory)
        ->page()->toBeInstanceOf(PageActionRequest::class);
});
