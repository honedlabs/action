<?php

declare(strict_types=1);

use Honed\Action\BulkAction;
use Honed\Action\ActionFactory;
use Honed\Action\Facades\Action;
use Honed\Action\InlineAction;
use Honed\Action\PageAction;

beforeEach(function () {
    $this->test = new ActionFactory;
});

it('makes bulk', function () {
    expect($this->test->new(ActionFactory::Bulk, 'test'))
        ->toBeInstanceOf(BulkAction::class);
});

it('makes inline', function () {
    expect($this->test->new(ActionFactory::Inline, 'test'))
        ->toBeInstanceOf(InlineAction::class);
});

it('makes page', function () {
    expect($this->test->new(ActionFactory::Page, 'test'))
        ->toBeInstanceOf(PageAction::class);
});

it('throws exception for invalid type', function () {
    $this->test->new('invalid', 'test');
})->throws(\InvalidArgumentException::class);

it('has facade', function () {
    expect(Action::bulk('test'))
        ->toBeInstanceOf(BulkAction::class);

    expect(Action::inline('test'))
        ->toBeInstanceOf(InlineAction::class);

    expect(Action::page('test'))
        ->toBeInstanceOf(PageAction::class);
});
