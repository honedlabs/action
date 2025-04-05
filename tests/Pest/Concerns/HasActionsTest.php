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
        ->actions([PageAction::make('view')])->toBe($this->test)
        ->actions([InlineAction::make('edit')])->toBe($this->test)
        ->getActions()->toHaveCount(2);
});

it('adds actions variadically', function () {
    expect($this->test)
        ->actions(PageAction::make('view'), InlineAction::make('edit'))->toBe($this->test)
        ->getActions()->toHaveCount(2);
});

it('adds actions collection', function () {
    expect($this->test)
        ->actions(collect([PageAction::make('view'), InlineAction::make('edit')]))->toBe($this->test)
        ->getActions()->toHaveCount(2);
});

it('adds action groups', function () {
    expect($this->test)
        ->actions(ActionGroup::make(PageAction::make('view')))->toBe($this->test)
        ->getActions()->toHaveCount(1);
});

it('can set all or none of the action types', function () {
    expect($this->test)
        ->hasAllActions()->toBeTrue()
        ->hasActions()->toBeTrue()
        ->hasNoActions()->toBeFalse()
        ->exceptActions()->toBe($this->test)
        ->hasAllActions()->toBeFalse()
        ->hasActions()->toBeFalse()
        ->hasNoActions()->toBeTrue()
        ->allActions()->toBe($this->test)
        ->hasAllActions()->toBeTrue()
        ->hasActions()->toBeTrue()
        ->hasNoActions()->toBeFalse();
});

it('toggles inline actions', function () {
    expect($this->test->actions())
        ->hasInlineActions()->toBeTrue()
        ->exceptInlineActions()->toBe($this->test)
        ->hasInlineActions()->toBeFalse()
        ->onlyInlineActions()->toBe($this->test)
        ->hasInlineActions()->toBeTrue();

    expect($this->test->actions(InlineAction::make('edit')))
        ->exceptInlineActions()->toBe($this->test)
        ->getInlineActions()->toBeEmpty();
});

it('toggles bulk actions', function () {
    expect($this->test)
        ->hasBulkActions()->toBeTrue()
        ->exceptBulkActions()->toBe($this->test)
        ->hasBulkActions()->toBeFalse()
        ->onlyBulkActions()->toBe($this->test)
        ->hasBulkActions()->toBeTrue();

    expect($this->test->actions(BulkAction::make('edit')))
        ->exceptBulkActions()->toBe($this->test)
        ->getBulkActions()->toBeEmpty();
});

it('toggles page actions', function () {
    expect($this->test)
        ->hasPageActions()->toBeTrue()
        ->exceptPageActions()->toBe($this->test)
        ->hasPageActions()->toBeFalse()
        ->onlyPageActions()->toBe($this->test)
        ->hasPageActions()->toBeTrue();

    expect($this->test->actions(PageAction::make('edit')))
        ->exceptPageActions()->toBe($this->test)
        ->getPageActions()->toBeEmpty();
});

it('has inline actions', function () {
    expect($this->test)
        ->actions([PageAction::make('create')])
        ->getInlineActions()->toHaveCount(0)
        ->actions([InlineAction::make('create')])
        ->getInlineActions()->toHaveCount(1);
});

it('has bulk actions', function () {
    expect($this->test)
        ->actions([PageAction::make('create')])
        ->getBulkActions()->toHaveCount(0)
        ->actions([BulkAction::make('create')])
        ->getBulkActions()->toHaveCount(1);
});

it('has page actions', function () {
    expect($this->test)
        ->actions([InlineAction::make('create')])
        ->getPageActions()->toHaveCount(0)
        ->actions([PageAction::make('create')])
        ->getPageActions()->toHaveCount(1);
});

it('has inline actions array representation', function () {
    $product = product();

    expect($this->test)
        ->actions([
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
        ->actions([BulkAction::make('create')])
        ->bulkActionsToArray()->toHaveCount(1);
});

it('has page actions array representation', function () {
    expect($this->test)
        ->actions([PageAction::make('create')])
        ->pageActionsToArray()->toHaveCount(1);
});