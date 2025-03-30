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
        ->getActions()->toBeEmpty();
});


it('adds actions', function () {
    expect($this->test)
        ->withActions([PageAction::make('view')])->toBe($this->test)
        ->withActions([InlineAction::make('edit')])->toBe($this->test)
        ->getActions()->toHaveCount(2);
});

it('adds actions variadically', function () {
    expect($this->test)
        ->withActions(PageAction::make('view'), InlineAction::make('edit'))->toBe($this->test)
        ->getActions()->toHaveCount(2);
});

it('adds actions collection', function () {
    expect($this->test)
        ->withActions(collect([PageAction::make('view'), InlineAction::make('edit')]))->toBe($this->test)
        ->getActions()->toHaveCount(2);
});

it('adds action groups', function () {
    expect($this->test)
        ->withActions(ActionGroup::make(PageAction::make('view')))->toBe($this->test)
        ->getActions()->toHaveCount(1);
});

it('has inline actions', function () {
    expect($this->test)
        ->withActions([PageAction::make('create')])
        ->getInlineActions()->toHaveCount(0)
        ->withActions([InlineAction::make('create')])
        ->getInlineActions()->toHaveCount(1);
});

it('has bulk actions', function () {
    expect($this->test)
        ->withActions([PageAction::make('create')])
        ->getBulkActions()->toHaveCount(0)
        ->withActions([BulkAction::make('create')])
        ->getBulkActions()->toHaveCount(1);
});

it('has page actions', function () {
    expect($this->test)
        ->withActions([InlineAction::make('create')])
        ->getPageActions()->toHaveCount(0)
        ->withActions([PageAction::make('create')])
        ->getPageActions()->toHaveCount(1);
});

it('has inline actions array representation', function () {
    $product = product();

    expect($this->test)
        ->withActions([
            InlineAction::make('create')
                ->label(fn ($product) => $product->name)
                ->allow(fn ($product) => $product->id % 2 === 1),
            InlineAction::make('edit')
                ->label(fn ($product) => $product->name)
                ->allow(fn ($product) => $product->id % 2 === 0),
        ])
        ->inlineActionsToArray($product)->toHaveCount(1);
});

it('has bulk actions array representation', function () {
    expect($this->test)
        ->withActions([BulkAction::make('create')])
        ->bulkActionsToArray()->toHaveCount(1);
});

it('has page actions array representation', function () {
    expect($this->test)
        ->withActions([PageAction::make('create')])
        ->pageActionsToArray()->toHaveCount(1);
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
            ->isWithoutActions()->toBeFalse()
            ->withoutActions()->toBe($this->test)
            ->isWithoutActions()->toBeTrue()
            ->getActions()->toHaveCount(3);
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