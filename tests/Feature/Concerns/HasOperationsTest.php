<?php

declare(strict_types=1);

use Honed\Action\Batch;
use Honed\Action\Concerns\HasOperations;
use Honed\Action\Operations\BulkOperation;
use Honed\Action\Operations\InlineOperation;
use Honed\Action\Operations\PageOperation;
use Honed\Core\Primitive;
use Workbench\App\Models\User;

beforeEach(function () {
    $this->test = new class() extends Primitive
    {
        use HasOperations;

        public function toArray($named = [], $typed = [])
        {
            return [];
        }
    };
});

it('adds actions', function () {
    expect($this->test)
        ->actions([PageOperation::make('view')])->toBe($this->test)
        ->actions([InlineOperation::make('edit')])->toBe($this->test)
        ->hasActions()->toBeTrue()
        ->getActions()->toHaveCount(2);
});

it('adds action groups', function () {
    expect($this->test)
        ->actions(Batch::make(PageOperation::make('view')))->toBe($this->test)
        ->hasActions()->toBeTrue()
        ->getActions()->toHaveCount(1);
});

it('provides actions', function () {
    expect($this->test)
        ->showsActions()->toBeTrue()
        ->hidesActions()->toBeFalse();
});

it('has inline actions', function () {
    expect($this->test)
        ->actions([PageOperation::make('create')])
        ->getInlineOperations()->toHaveCount(0)
        ->actions([InlineOperation::make('create')])
        ->getInlineOperations()->toHaveCount(1);
});

it('has bulk actions', function () {
    expect($this->test)
        ->actions([PageOperation::make('create')])
        ->getBulkOperations()->toHaveCount(0)
        ->actions([BulkOperation::make('create')])
        ->getBulkOperations()->toHaveCount(1);
});

it('has page actions', function () {
    expect($this->test)
        ->actions([InlineOperation::make('create')])
        ->getPageOperations()->toHaveCount(0)
        ->actions([PageOperation::make('create')])
        ->getPageOperations()->toHaveCount(1);
});

it('has inline actions array representation', function () {
    $user = User::factory()->create();

    expect($this->test)
        ->actions([
            InlineOperation::make('create')
                ->label(fn ($user) => $user->name)
                ->allow(fn ($user) => $user->id % 2 === 1),
            InlineOperation::make('edit')
                ->label(fn ($user) => $user->name)
                ->allow(fn ($user) => $user->id % 2 === 0),
        ])
        ->inlineActionsToArray($user)->toHaveCount(1);
});

it('has bulk actions array representation', function () {
    expect($this->test)
        ->actions([BulkOperation::make('create')])
        ->bulkActionsToArray()->toHaveCount(1);
});

it('has page actions array representation', function () {
    expect($this->test)
        ->actions([PageOperation::make('create')])
        ->pageActionsToArray()->toHaveCount(1);
});
