<?php

declare(strict_types=1);

use Honed\Action\ActionGroup;
use Honed\Action\BulkAction;
use Honed\Action\Concerns\HasActions;
use Honed\Action\InlineAction;
use Honed\Action\PageAction;
use Honed\Core\Primitive;
use Workbench\App\Models\User;

beforeEach(function () {
    $this->test = new class() extends Primitive
    {
        use HasActions;

        public function toArray($named = [], $typed = [])
        {
            return [];
        }
    };
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

it('adds action groups', function () {
    expect($this->test)
        ->withActions(ActionGroup::make(PageAction::make('view')))->toBe($this->test)
        ->hasActions()->toBeTrue()
        ->getActions()->toHaveCount(1);
});

it('provides actions', function () {
    expect($this->test)
        ->showsActions()->toBeTrue()
        ->hidesActions()->toBeFalse();
});

it('provides inline actions', function () {
    expect($this->test->withActions(InlineAction::make('edit')))
        ->showsInlineActions()->toBeTrue()
        ->hidesInlineActions()->toBeFalse()
        ->hideInlineActions()->toBe($this->test)
        ->showsInlineActions()->toBeFalse()
        ->hidesInlineActions()->toBeTrue()
        ->showInlineActions()->toBe($this->test)
        ->showsInlineActions()->toBeTrue()
        ->hidesInlineActions()->toBeFalse();
});

it('provides bulk actions', function () {
    expect($this->test->withActions(BulkAction::make('edit')))
        ->showsBulkActions()->toBeTrue()
        ->hidesBulkActions()->toBeFalse()
        ->hideBulkActions()->toBe($this->test)
        ->showsBulkActions()->toBeFalse()
        ->hidesBulkActions()->toBeTrue()
        ->showBulkActions()->toBe($this->test)
        ->showsBulkActions()->toBeTrue()
        ->hidesBulkActions()->toBeFalse();
});

it('provides page actions', function () {
    expect($this->test->withActions(PageAction::make('edit')))
        ->showsPageActions()->toBeTrue()
        ->hidesPageActions()->toBeFalse()
        ->hidePageActions()->toBe($this->test)
        ->showsPageActions()->toBeFalse()
        ->hidesPageActions()->toBeTrue()
        ->showPageActions()->toBe($this->test)
        ->showsPageActions()->toBeTrue()
        ->hidesPageActions()->toBeFalse();
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
    $user = User::factory()->create();

    expect($this->test)
        ->withActions([
            InlineAction::make('create')
                ->label(fn ($user) => $user->name)
                ->allow(fn ($user) => $user->id % 2 === 1),
            InlineAction::make('edit')
                ->label(fn ($user) => $user->name)
                ->allow(fn ($user) => $user->id % 2 === 0),
        ])
        ->inlineActionsToArray($user)->toHaveCount(1);
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
