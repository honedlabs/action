<?php

declare(strict_types=1);

use Honed\Action\BulkAction;
use Honed\Action\PageAction;
use Honed\Action\ActionGroup;
use Honed\Action\InlineAction;
use Honed\Action\Concerns\HasActions;
use Honed\Core\Primitive;

beforeEach(function () {
    $this->test = new class extends Primitive{
        use HasActions;

        public function toArray()
        {
            return [];
        }
    };
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

it('provides inline actions', function () {
    expect($this->test->actions(InlineAction::make('edit')))
        ->providesInlineActions()->toBeTrue()
        ->getInlineActions()->toHaveCount(1)
        ->exceptInlineActions()->toBe($this->test)
        ->providesInlineActions()->toBeFalse()
        ->getInlineActions()->toBeEmpty()
        ->onlyInlineActions()->toBe($this->test)
        ->providesInlineActions()->toBeTrue()
        ->getInlineActions()->toHaveCount(1);
});

it('provides bulk actions', function () {
    expect($this->test->actions(BulkAction::make('edit')))
        ->providesBulkActions()->toBeTrue()
        ->getBulkActions()->toHaveCount(1)
        ->exceptBulkActions()->toBe($this->test)
        ->providesBulkActions()->toBeFalse()
        ->getBulkActions()->toBeEmpty()
        ->onlyBulkActions()->toBe($this->test)
        ->providesBulkActions()->toBeTrue()
        ->getBulkActions()->toHaveCount(1);
});

it('provides page actions', function () {
    expect($this->test->actions(PageAction::make('edit')))
        ->providesPageActions()->toBeTrue()
        ->getPageActions()->toHaveCount(1)
        ->exceptPageActions()->toBe($this->test)
        ->providesPageActions()->toBeFalse()
        ->getPageActions()->toBeEmpty()
        ->onlyPageActions()->toBe($this->test)
        ->providesPageActions()->toBeTrue()
        ->getPageActions()->toHaveCount(1);
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