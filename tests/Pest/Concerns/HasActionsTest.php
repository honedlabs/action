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
        ->toBeCollection()
        ->toBeEmpty()
        );

    expect($this->method)
        ->hasActions()->toBeTrue()
        ->getActions()->scoped(fn ($actions) => $actions
        ->toBeCollection()
        ->toHaveCount(5)
        );
});

it('has inline actions', function () {
    expect($this->test)
        ->inlineActions()->toBeCollection()
        ->toBeEmpty();

    expect($this->method)
        ->inlineActions()->toBeCollection()
        ->toHaveCount(1);
});

it('has bulk actions', function () {
    expect($this->test)
        ->bulkActions()->toBeCollection()
        ->toBeEmpty();

    expect($this->method)
        ->bulkActions()->toBeCollection()
        ->toHaveCount(1);
});

it('has page actions', function () {
    expect($this->test)
        ->pageActions()->toBeCollection()
        ->toBeEmpty();

    expect($this->method)
        ->pageActions()->toBeCollection()
        ->toHaveCount(1);
});
