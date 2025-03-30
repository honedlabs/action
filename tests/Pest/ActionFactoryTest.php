<?php

declare(strict_types=1);

use Honed\Action\BulkAction;
use Honed\Action\PageAction;
use Honed\Action\ActionGroup;
use Honed\Action\InlineAction;
use Honed\Action\ActionFactory;
use Honed\Action\Facades\Action;

it('makes bulk', function () {
    expect(Action::new(ActionFactory::Bulk, 'test'))
        ->toBeInstanceOf(BulkAction::class);

    expect(Action::bulk('test'))
        ->toBeInstanceOf(BulkAction::class);
});

it('makes inline', function () {
    expect(Action::new(ActionFactory::Inline, 'test'))
        ->toBeInstanceOf(InlineAction::class);

    expect(Action::inline('test'))
        ->toBeInstanceOf(InlineAction::class);
});

it('makes page', function () {
    expect(Action::new(ActionFactory::Page, 'test'))
        ->toBeInstanceOf(PageAction::class);

    expect(Action::page('test'))
        ->toBeInstanceOf(PageAction::class);
});

it('makes group', function () {
    expect(Action::group(Action::new(ActionFactory::Bulk, 'test')))
        ->toBeInstanceOf(ActionGroup::class);
});

it('throws exception for invalid type', function () {
    Action::new('invalid', 'test');
})->throws(\InvalidArgumentException::class);