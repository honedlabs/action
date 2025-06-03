<?php

declare(strict_types=1);

use Honed\Action\PageAction;

beforeEach(function () {
    $this->action = PageAction::make('edit');
});

it('has page type', function () {
    expect($this->action)
        ->getType()->toBe('page')
        ->isPage()->toBeTrue();
});
