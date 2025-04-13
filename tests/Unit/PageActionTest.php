<?php

declare(strict_types=1);

use Honed\Action\PageAction;
use Honed\Action\Support\Constants;

beforeEach(function () {
    $this->action = PageAction::make('edit');
});

it('has page type', function () {
    expect($this->action)
        ->getType()->toBe(Constants::PAGE);
});
