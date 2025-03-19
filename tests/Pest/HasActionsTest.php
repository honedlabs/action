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

it('is empty by default', function () {
    expect($this->test)
        ->hasActions()->toBeFalse()
        ->getActions()->toBeEmpty();
});


it('adds actions', function () {
    expect($this->test)
        ->withActions([PageAction::make('view')])->toBe($this->test)
        ->withActions([InlineAction::make('edit')])->toBe($this->test)
        ->hasActions()->toBeTrue()
        ->getActions()->toHaveCount(2);
});

it('adds actions variadically', function () {
    expect($this->test)
        ->withActions(PageAction::make('view'), InlineAction::make('edit'))->toBe($this->test)
        ->hasActions()->toBeTrue()
        ->getActions()->toHaveCount(2);
});

it('adds actions collection', function () {
    expect($this->test)
        ->withActions(collect([PageAction::make('view'), InlineAction::make('edit')]))->toBe($this->test)
        ->hasActions()->toBeTrue()
        ->getActions()->toHaveCount(2);
});

it('has inline actions', function () {
    expect($this->test)
        ->hasActions()->toBeFalse()
        ->withActions([PageAction::make('create')])
        ->hasActions()->toBeTrue()
        ->getInlineActions()->toHaveCount(0)
        ->withActions([InlineAction::make('create')])
        ->hasActions()->toBeTrue()
        ->getInlineActions()->toHaveCount(1);
});

it('has bulk actions', function () {
    expect($this->test)
        ->hasActions()->toBeFalse()
        ->withActions([PageAction::make('create')])
        ->hasActions()->toBeTrue()
        ->getBulkActions()->toHaveCount(0)
        ->withActions([BulkAction::make('create')])
        ->hasActions()->toBeTrue()
        ->getBulkActions()->toHaveCount(1);
});

it('has page actions', function () {
    expect($this->test)
        ->hasActions()->toBeFalse()
        ->withActions([InlineAction::make('create')])
        ->hasActions()->toBeTrue()
        ->getPageActions()->toHaveCount(0)
        ->withActions([PageAction::make('create')])
        ->hasActions()->toBeTrue()
        ->getPageActions()->toHaveCount(1);
});

it('has array representation', function () {
    expect($this->test)
        ->withActions([PageAction::make('create')])
        ->actionsToArray()->toHaveKeys(['hasInline', 'bulk', 'page']);
});

describe('without', function () {
    beforeEach(function () {
        $this->test = $this->test->withActions(
            InlineAction::make('create'), 
            BulkAction::make('create'), 
            PageAction::make('create')
        );
    });

    test('all', function () {
        expect($this->test)
            ->hasActions()->toBeTrue()
            ->withoutActions()->toBe($this->test)
            ->hasActions()->toBeFalse();
    });

    test('inline', function () {
        expect($this->test)
            ->isWithoutInlineActions()->toBeFalse()
            ->getInlineActions()->toHaveCount(1)
            ->withoutInlineActions()->toBe($this->test)
            ->isWithoutInlineActions()->toBeTrue()
            ->getInlineActions()->toBeEmpty();
    });

    test('bulk', function () {
        expect($this->test)
            ->isWithoutBulkActions()->toBeFalse()
            ->getBulkActions()->toHaveCount(1)
            ->withoutBulkActions()->toBe($this->test)
            ->isWithoutBulkActions()->toBeTrue()
            ->getBulkActions()->toBeEmpty();
    });

    test('page', function () {
        expect($this->test)
            ->isWithoutPageActions()->toBeFalse()
            ->getPageActions()->toHaveCount(1)
            ->withoutPageActions()->toBe($this->test)
            ->isWithoutPageActions()->toBeTrue()
            ->getPageActions()->toBeEmpty();
    });
});