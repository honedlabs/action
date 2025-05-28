<?php

declare(strict_types=1);

use Honed\Action\ActionGroup;
use Honed\Action\BulkAction;
use Honed\Action\Facades\Action;
use Honed\Action\InlineAction;
use Honed\Action\PageAction;
use Honed\Action\Support\Constants;

it('makes bulk', function () {
    expect(Action::new(Constants::BULK, 'test'))
        ->toBeInstanceOf(BulkAction::class);

    expect(Action::bulk('test'))
        ->toBeInstanceOf(BulkAction::class);
});

it('makes inline', function () {
    expect(Action::new(Constants::INLINE, 'test'))
        ->toBeInstanceOf(InlineAction::class);

    expect(Action::inline('test'))
        ->toBeInstanceOf(InlineAction::class);
});

it('makes page', function () {
    expect(Action::new(Constants::PAGE, 'test'))
        ->toBeInstanceOf(PageAction::class);

    expect(Action::page('test'))
        ->toBeInstanceOf(PageAction::class);
});

it('makes group', function () {
    expect(Action::group(Action::new(Constants::BULK, 'test')))
        ->toBeInstanceOf(ActionGroup::class);
});

it('throws exception for invalid type', function () {
    Action::new('invalid', 'test');
})->throws(\InvalidArgumentException::class);
