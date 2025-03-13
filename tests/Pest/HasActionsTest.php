<?php

declare(strict_types=1);

use Honed\Action\BulkAction;
use Honed\Action\PageAction;
use Honed\Action\ActionGroup;
use Honed\Action\InlineAction;
use Honed\Action\Concerns\HasActions;

beforeEach(function () {
    $this->test = new class {
        use HasActions;
    };
});

it('adds action', function () {
    expect($this->test)
        ->addAction(PageAction::make('create'))
        ->hasActions()->toBeTrue()
        ->getActions()->toHaveCount(1);
});

it('adds actions', function () {
    expect($this->test)
        ->hasActions()->toBeFalse()
        ->addActions([PageAction::make('create')])
        ->hasActions()->toBeTrue()
        ->getActions()->toHaveCount(1);
});

it('has inline actions', function () {
    expect($this->test)
        ->hasActions()->toBeFalse()
        ->addActions([PageAction::make('create')])
        ->hasActions()->toBeTrue()
        ->getInlineActions()->toHaveCount(0)
        ->addActions([InlineAction::make('create')])
        ->hasActions()->toBeTrue()
        ->getInlineActions()->toHaveCount(1);
});

it('has bulk actions', function () {
    expect($this->test)
        ->hasActions()->toBeFalse()
        ->addActions([PageAction::make('create')])
        ->hasActions()->toBeTrue()
        ->getBulkActions()->toHaveCount(0)
        ->addActions([BulkAction::make('create')])
        ->hasActions()->toBeTrue()
        ->getBulkActions()->toHaveCount(1);
});

it('has page actions', function () {
    expect($this->test)
        ->hasActions()->toBeFalse()
        ->addActions([InlineAction::make('create')])
        ->hasActions()->toBeTrue()
        ->getPageActions()->toHaveCount(0)
        ->addActions([PageAction::make('create')])
        ->hasActions()->toBeTrue()
        ->getPageActions()->toHaveCount(1);
});

it('has array representation', function () {
    expect($this->test)
        ->addActions([PageAction::make('create')])
        ->actionsToArray()->toHaveKeys(['hasInline', 'bulk', 'page']);
});

it('can go without actions', function () {
    expect($this->test)
        ->addActions([PageAction::make('create')])
        ->hasActions()->toBeTrue()
        ->isWithoutActions()->toBeFalse()
        ->withoutActions()->toBe($this->test)
        ->hasActions()->toBeFalse()
        ->isWithoutActions()->toBeTrue()
        ->getActions()->toBeEmpty();
});

it('gets record actions', function () {
    expect($this->test)
        ->addActions([InlineAction::make('create')])
        ->getRecordActions([], [])->toHaveCount(1);
});
