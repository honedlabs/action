<?php

declare(strict_types=1);

use Honed\Action\Operations\BulkOperation;
use Honed\Action\Operations\InlineOperation;
use Honed\Action\Operations\PageOperation;
use Workbench\App\Assemblers\UpdateAssembler;

beforeEach(function () {
    $this->assembler = UpdateAssembler::make();
});

it('creates inline operation', function () {
    expect($this->assembler->inline())
        ->toBeInstanceOf(InlineOperation::class)
        ->isInline()->toBeTrue()
        ->getName()->toBe('update')
        ->getLabel()->toBe('Update')
        ->getIcon()->toBe('heroicon-o-pencil');
});

it('creates bulk operation', function () {
    expect($this->assembler->bulk())
        ->toBeInstanceOf(BulkOperation::class)
        ->isBulk()->toBeTrue()
        ->getName()->toBe('update_bulk')
        ->getLabel()->toBe('Update')
        ->getIcon()->toBe('heroicon-o-pencil');
});

it('creates page operation', function () {
    expect($this->assembler->page())
        ->toBeInstanceOf(PageOperation::class)
        ->isPage()->toBeTrue()
        ->getName()->toBe('update_page')
        ->getLabel()->toBe('Update')
        ->getIcon()->toBe('heroicon-o-pencil');
});
