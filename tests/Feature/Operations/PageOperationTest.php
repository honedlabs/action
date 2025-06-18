<?php

declare(strict_types=1);

use Honed\Action\Operations\PageOperation;

beforeEach(function () {
    $this->action = PageOperation::make('edit');
});

it('has page type', function () {
    expect($this->action)
        ->getType()->toBe(PageOperation::PAGE)
        ->isPage()->toBeTrue();
});
