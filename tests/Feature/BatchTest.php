<?php

declare(strict_types=1);

use Honed\Action\Batch;
use Honed\Action\Operations\PageOperation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Workbench\App\Batches\UserBatch;
use Workbench\App\Models\User;

beforeEach(function () {
    $this->batch = Batch::make(PageOperation::make('create'));
});

afterEach(function () {
    Batch::flushState();
});

it('has model', function () {
    expect($this->batch)
        ->getRecord()->toBeNull()
        ->record(User::factory()->create())->toBe($this->batch)
        ->getRecord()->toBeInstanceOf(User::class);
});

it('resolves batch', function () {
    UserBatch::guessBatchNamesUsing(function ($class) {
        return Str::of($class)
            ->classBasename()
            ->prepend('Workbench\\App\\Batches\\')
            ->append('Batch')
            ->value();
    });

    expect(UserBatch::resolveBatchName(User::class))
        ->toBe(UserBatch::class);

    expect(UserBatch::batchForModel(User::class))
        ->toBeInstanceOf(UserBatch::class);
});

it('uses namespace', function () {
    Batch::useNamespace('');

    expect(UserBatch::resolveBatchName(User::class))
        ->toBe(Str::of(UserBatch::class)
            ->classBasename()
            ->prepend('Models\\')
            ->value()
        );
});

it('has array representation with actions but is anonymous', function () {
    expect($this->batch->toArray())
        ->toBeArray()
        ->toHaveKeys([
            'inline',
            'bulk',
            'page',
        ]);
});

describe('class-based batch', function () {
    beforeEach(function () {
        $this->batch = UserBatch::make()->record(User::factory()->create());
    });

    it('has array representation with actions', function () {
        expect($this->batch->toArray())
            ->toBeArray()
            ->toHaveKeys([
                'inline',
                'bulk',
                'page',
            ]);
    });

    it('has array representation without actions', function () {
        expect($this->batch->actionable(false)->toArray())
            ->toBeArray()
            ->toHaveKeys([
                'inline',
                'bulk',
                'page',
            ]);
    });
});

describe('evaluation', function () {
    beforeEach(function () {
        $this->batch = UserBatch::make()
            ->record(User::factory()->create());
    });

    it('named dependencies', function ($closure, $class) {
        expect($this->batch->evaluate($closure))->toBeInstanceOf($class);
    })->with([
        'row' => fn () => [fn ($row) => $row, User::class],
        'record' => fn () => [fn ($record) => $record, User::class],
        'batch' => fn () => [fn ($batch) => $batch, UserBatch::class],
    ]);

    it('typed dependencies', function ($closure, $class) {
        expect($this->batch->evaluate($closure))->toBeInstanceOf($class);
    })->with([
        'model' => fn () => [fn (Model $arg) => $arg, User::class],
        'user' => fn () => [fn (User $arg) => $arg, User::class],
    ]);
});
