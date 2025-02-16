<?php

declare(strict_types=1);

use Honed\Action\BulkAction;
use Honed\Action\Concerns\HasActions;
use Honed\Action\InlineAction;
use Honed\Action\PageAction;

class HasActionsTest
{
    use HasActions;
}

class HasActionsMethod extends HasActionsTest
{
    public function actions()
    {
        return [
            InlineAction::make('edit.product'),
            BulkAction::make('delete.products'),
            BulkAction::make('restore.products')->allow(false),
            PageAction::make('create.product'),
            PageAction::make('show.product')->allow(false),
        ];
    }
}

beforeEach(function () {
    $this->test = new HasActionsTest;
    $this->method = new HasActionsMethod;
});

it('has actions', function () {
    expect($this->test)
        ->hasActions()->toBeFalse()
        ->getActions()->scoped(fn ($actions) => $actions
        ->toBeArray()
        ->toBeEmpty()
        );

    expect($this->method)
        ->hasActions()->toBeTrue()
        ->getActions()->scoped(fn ($actions) => $actions
        ->toBeArray()
        ->toHaveCount(5)
        );
});

it('has inline actions', function () {
    expect($this->test)
        ->getInlineActions()->toBeArray()
        ->toBeEmpty();

    expect($this->method)
        ->getInlineActions()->toBeArray()
        ->toHaveCount(1);
});

it('has bulk actions', function () {
    expect($this->test)
        ->getBulkActions()->toBeArray()
        ->toBeEmpty();

    expect($this->method)
        ->getBulkActions()->toBeArray()
        ->toHaveCount(1);
});

it('has page actions', function () {
    expect($this->test)
        ->getPageActions()->toBeArray()
        ->toBeEmpty();

    expect($this->method)
        ->getPageActions()->toBeArray()
        ->toHaveCount(1);
});

it('has array representation', function () {
    expect($this->test->actionsToArray())
        ->toBeArray()
        ->toEqual([
            'actions' => false,
            'bulk' => [],
            'page' => [],
        ]);
});
