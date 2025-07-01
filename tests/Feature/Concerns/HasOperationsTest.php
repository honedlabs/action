<?php

declare(strict_types=1);

use Honed\Action\Batch;
use Honed\Action\Operations\BulkOperation;
use Honed\Action\Operations\InlineOperation;
use Honed\Action\Operations\PageOperation;

beforeEach(function () {
    $this->batch = Batch::make();
});

it('can be operable', function () {
    expect($this->batch)
        ->operations(InlineOperation::make('create'))->toBe($this->batch)
        ->isOperable()->toBeTrue()
        ->getOperations()->toHaveCount(1)
        ->notOperable()->toBe($this->batch)
        ->isNotOperable()->toBeTrue()
        ->getOperations()->toBeEmpty();
});

it('adds operations', function () {
    expect($this->batch)
        ->operations([PageOperation::make('view')])->toBe($this->batch)
        ->operations([InlineOperation::make('edit')])->toBe($this->batch)
        ->getOperations()->toHaveCount(2);
});

it('adds batches', function () {
    expect($this->batch)
        ->operations(Batch::make(PageOperation::make('view')))->toBe($this->batch)
        ->getOperations()->toHaveCount(1);
});

it('adds operation', function () {
    expect($this->batch)
        ->operation(PageOperation::make('view'))->toBe($this->batch)
        ->operation(InlineOperation::make('edit'))->toBe($this->batch)
        ->getOperations()->toHaveCount(2);
});

describe('inline operations', function () {
    beforeEach(function () {
        $this->batch->operations([
            InlineOperation::make('edit'),
            BulkOperation::make('delete'),
            PageOperation::make('create'),
        ]);
    });

    it('provides', function () {
        expect($this->batch)
            ->isInlinable()->toBeTrue()
            ->getInlineOperations()->toHaveCount(1)
            ->notInlinable()->toBe($this->batch)
            ->isNotInlinable()->toBeTrue()
            ->getInlineOperations()->toHaveCount(0);
    });

    it('adds', function () {
        expect($this->batch)
            ->inlineOperations([InlineOperation::make('new')])->toBe($this->batch)
            ->getInlineOperations()->toHaveCount(2)
            ->inlineOperations(false)->toBe($this->batch)
            ->isNotInlinable()->toBeTrue()
            ->getInlineOperations()->toHaveCount(0);
    });

    it('removes disallowed actions from array representation', function () {
        expect($this->batch)
            ->inlineOperations([
                InlineOperation::make('new')->allow(fn () => false),
            ])->toBe($this->batch)
            ->inlineOperationsToArray()->toHaveCount(1);
    });

    it('has array representation without action or route', function () {
        expect($this->batch)
            ->inlineOperationsToArray()
            ->scoped(fn ($operations) => $operations
                ->toBeArray()
                ->toHaveCount(1)
                ->{0}
                ->scoped(fn ($operation) => $operation
                    ->toBeArray()
                    ->toHaveKeys([
                        'name',
                        'label',
                        'default',
                    ])->not->toHaveKeys([
                        'type',
                        'confirm',
                        'icon',
                        'href',
                        'method',
                        'target',
                    ])
                )
            );
    });

    it('has array representation with action', function () {
        expect($this->batch)
            ->inlineOperations([
                InlineOperation::make('action')->action(fn () => false),
            ])->toBe($this->batch)
            ->inlineOperationsToArray()
            ->scoped(fn ($operations) => $operations
                ->toBeArray()
                ->toHaveCount(2)
                ->{1}
                ->scoped(fn ($operation) => $operation
                    ->toBeArray()
                    ->toHaveKeys([
                        'name',
                        'label',
                        'type',
                        'href',
                        'method',
                    ])->not->toHaveKeys([
                        'confirm',
                        'icon',
                        'target',
                    ])
                    ->{'type'}->toBe('inertia')
                    ->{'method'}->toBe('post')
                    ->{'href'}->toBe(route(config('action.name'), [$this->batch, 'action']))
                )
            );
    });

    it('has array representation with url', function () {
        expect($this->batch)
            ->inlineOperations([
                InlineOperation::make('action')->url('users.index')->notInertia(),
            ])->toBe($this->batch)
            ->inlineOperationsToArray()
            ->scoped(fn ($operations) => $operations
                ->toBeArray()
                ->toHaveCount(2)
                ->{1}
                ->scoped(fn ($operation) => $operation
                    ->toBeArray()
                    ->toHaveKeys([
                        'name',
                        'label',
                        'type',
                        'href',
                        'method',
                    ])->not->toHaveKeys([
                        'confirm',
                        'icon',
                        'target',
                    ])
                    ->{'type'}->toBe('anchor')
                    ->{'method'}->toBe('get')
                    ->{'href'}->toBe(route('users.index'))
                )
            );
    });
});

describe('bulk operations', function () {
    beforeEach(function () {
        $this->batch->operations([
            InlineOperation::make('edit'),
            BulkOperation::make('delete'),
            PageOperation::make('create'),
        ]);
    });

    it('provides', function () {
        expect($this->batch)
            ->isBulkable()->toBeTrue()
            ->getBulkOperations()->toHaveCount(1)
            ->notBulkable()->toBe($this->batch)
            ->isNotBulkable()->toBeTrue()
            ->getBulkOperations()->toBeEmpty();
    });

    it('adds', function () {
        expect($this->batch)
            ->bulkOperations([BulkOperation::make('new')])->toBe($this->batch)
            ->getBulkOperations()->toHaveCount(2)
            ->bulkOperations(false)->toBe($this->batch)
            ->isNotBulkable()->toBeTrue()
            ->getBulkOperations()->toBeEmpty();
    });

    it('removes disallowed actions from array representation', function () {
        expect($this->batch)
            ->bulkOperations([
                BulkOperation::make('new')->allow(fn () => false),
            ])->toBe($this->batch)
            ->bulkOperationsToArray()->toHaveCount(1);
    });

    it('has array representation without action or route', function () {
        expect($this->batch)
            ->bulkOperationsToArray()
            ->scoped(fn ($operations) => $operations
                ->toBeArray()
                ->toHaveCount(1)
                ->{0}
                ->scoped(fn ($operation) => $operation
                    ->toBeArray()
                    ->toHaveKeys([
                        'name',
                        'label',
                        'keep',
                    ])->not->toHaveKeys([
                        'type',
                        'confirm',
                        'icon',
                        'href',
                        'method',
                        'target',
                    ])
                )
            );
    });

    it('has array representation with action', function () {
        expect($this->batch)
            ->bulkOperations([
                BulkOperation::make('action')->action(fn () => false),
            ])->toBe($this->batch)
            ->bulkOperationsToArray()
            ->scoped(fn ($operations) => $operations
                ->toBeArray()
                ->toHaveCount(2)
                ->{1}
                ->scoped(fn ($operation) => $operation
                    ->toBeArray()
                    ->toHaveKeys([
                        'name',
                        'label',
                        'type',
                        'href',
                        'method',
                        'keep',
                    ])->not->toHaveKeys([
                        'confirm',
                        'icon',
                        'target',
                    ])
                    ->{'keep'}->toBeFalse()
                    ->{'type'}->toBe('inertia')
                    ->{'method'}->toBe('post')
                    ->{'href'}->toBe(route(config('action.name'), [$this->batch, 'action']))
                )
            );
    });

    it('has array representation with url', function () {
        expect($this->batch)
            ->bulkOperations([
                BulkOperation::make('action')->url('users.index')->notInertia(),
            ])->toBe($this->batch)
            ->bulkOperationsToArray()
            ->scoped(fn ($operations) => $operations
                ->toBeArray()
                ->toHaveCount(2)
                ->{1}
                ->scoped(fn ($operation) => $operation
                    ->toBeArray()
                    ->toHaveKeys([
                        'name',
                        'label',
                        'type',
                        'href',
                        'method',
                        'keep',
                    ])->not->toHaveKeys([
                        'confirm',
                        'icon',
                        'target',
                    ])
                    ->{'keep'}->toBeFalse()
                    ->{'type'}->toBe('anchor')
                    ->{'method'}->toBe('get')
                    ->{'href'}->toBe(route('users.index'))
                )
            );
    });
});

describe('page operations', function () {
    beforeEach(function () {
        $this->batch->operations([
            PageOperation::make('create'),
            InlineOperation::make('edit'),
            BulkOperation::make('delete'),
        ]);
    });

    it('provides', function () {
        expect($this->batch)
            ->isPageable()->toBeTrue()
            ->getPageOperations()->toHaveCount(1)
            ->notPageable()->toBe($this->batch)
            ->isNotPageable()->toBeTrue()
            ->getPageOperations()->toBeEmpty();
    });

    it('adds', function () {
        expect($this->batch)
            ->pageOperations([PageOperation::make('new')])->toBe($this->batch)
            ->getPageOperations()->toHaveCount(2)
            ->pageOperations(false)->toBe($this->batch)
            ->isNotPageable()->toBeTrue()
            ->getPageOperations()->toBeEmpty();
    });

    it('removes disallowed actions from array representation', function () {
        expect($this->batch)
            ->pageOperations([
                PageOperation::make('new')->allow(fn () => false),
            ])->toBe($this->batch)
            ->pageOperationsToArray()->toHaveCount(1);
    });

    it('has array representation without action or route', function () {
        expect($this->batch)
            ->pageOperationsToArray()
            ->scoped(fn ($operations) => $operations
                ->toBeArray()
                ->toHaveCount(1)
                ->{0}
                ->scoped(fn ($operation) => $operation
                    ->toBeArray()
                    ->toHaveKeys([
                        'name',
                        'label',
                    ])->not->toHaveKeys([
                        'type',
                        'confirm',
                        'icon',
                        'href',
                        'method',
                        'target',
                    ])
                )
            );
    });

    it('has array representation with action', function () {
        expect($this->batch)
            ->pageOperations([
                PageOperation::make('action')->action(fn () => false),
            ])->toBe($this->batch)
            ->pageOperationsToArray()
            ->scoped(fn ($operations) => $operations
                ->toBeArray()
                ->toHaveCount(2)
                ->{1}
                ->scoped(fn ($operation) => $operation
                    ->toBeArray()
                    ->toHaveKeys([
                        'name',
                        'label',
                        'type',
                        'href',
                        'method',
                    ])->not->toHaveKeys([
                        'confirm',
                        'icon',
                        'target',
                    ])
                    ->{'type'}->toBe('inertia')
                    ->{'method'}->toBe('post')
                    ->{'href'}->toBe(route(config('action.name'), [$this->batch, 'action']))
                )
            );
    });

    it('has array representation with url', function () {
        expect($this->batch)
            ->pageOperations([
                PageOperation::make('action')->url('users.index')->notInertia(),
            ])->toBe($this->batch)
            ->pageOperationsToArray()
            ->scoped(fn ($operations) => $operations
                ->toBeArray()
                ->toHaveCount(2)
                ->{1}
                ->scoped(fn ($operation) => $operation
                    ->toBeArray()
                    ->toHaveKeys([
                        'name',
                        'label',
                        'type',
                        'href',
                        'method',
                    ])->not->toHaveKeys([
                        'confirm',
                        'icon',
                        'target',
                    ])
                    ->{'type'}->toBe('anchor')
                    ->{'method'}->toBe('get')
                    ->{'href'}->toBe(route('users.index'))
                )
            );
    });
});
