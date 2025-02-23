<?php

declare(strict_types=1);

use Honed\Action\BulkAction;
use Honed\Action\PageAction;
use Honed\Action\InlineAction;
use Honed\Action\Concerns\HasActions;
use Honed\Action\Tests\Fixtures\BlankActions;
use Honed\Action\Tests\Fixtures\FilledActions;

beforeEach(function () {
    $this->test = new BlankActions;
    $this->method = new FilledActions;
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

it('adds actions', function () {
    $action = InlineAction::make('edit.product');

    expect($this->test)
        ->addAction($action)->toBe($this->test)
        ->getActions()->toHaveCount(1);

    expect($this->test->addActions([$action]))
        ->toBe($this->test)
        ->getActions()->toHaveCount(2);

    expect($this->test->addActions(collect([InlineAction::make('edit.product')])))
        ->toBe($this->test)
        ->getActions()->toHaveCount(3);
});
