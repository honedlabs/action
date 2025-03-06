<?php

declare(strict_types=1);

use Honed\Action\PageAction;
use Honed\Action\ActionGroup;
use Honed\Action\InlineAction;

it('makes', function () {
    expect(ActionGroup::make(PageAction::make('create'), PageAction::make('delete')))
        ->toBeInstanceOf(ActionGroup::class)
        ->getActions()->toHaveCount(2);
});

it('has actions called', function () {
    expect(ActionGroup::make()->actions([PageAction::make('create'), PageAction::make('delete')]))
        ->toBeInstanceOf(ActionGroup::class)
        ->getActions()->toHaveCount(2);
});

it('has array representation', function () {
    expect(ActionGroup::make()->toArray())
        ->toBeArray()
        ->toBeEmpty();
});

it('only has page actions', function () {
    expect(ActionGroup::make()->actions([PageAction::make('create'), InlineAction::make('delete')]))
        ->toBeInstanceOf(ActionGroup::class)
        ->toArray()->toHaveCount(1);
});


