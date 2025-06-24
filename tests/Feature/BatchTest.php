<?php

declare(strict_types=1);

use Honed\Action\Batch;
use Honed\Action\Operations\PageOperation;
use Honed\Action\Testing\RequestFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Workbench\App\Batches\UserBatch;
use Workbench\App\Models\User;

beforeEach(function () {
    $this->group = Batch::make(PageOperation::make('create'));
});

afterEach(function () {
    Batch::flushState();
});

it('has model', function () {
    expect($this->group)
        ->getRecord()->toBeNull()
        ->record(User::factory()->create())->toBe($this->group)
        ->getRecord()->toBeInstanceOf(User::class);
});

it('has route key name', function () {
    expect($this->group)
        ->getRouteKeyName()->toBe('action');
});

it('requires builder to handle requests', function () {
    $request = RequestFactory::page()
        ->fill()
        ->validate();

    expect($this->group->handle($request))
        ->toBeInstanceOf(RedirectResponse::class);
})->throws(RuntimeException::class);

it('resolves route binding', function () {
    expect($this->group)
        ->resolveRouteBinding($this->group->getRouteKey())
        ->toBeNull();

    $actions = UserBatch::make();

    expect($actions)
        ->resolveRouteBinding($actions->getRouteKey())
        ->toBeInstanceOf(UserBatch::class);

    expect($actions)
        ->resolveChildRouteBinding(UserBatch::class, $actions->getRouteKey())
        ->toBeInstanceOf(UserBatch::class);
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
    expect($this->group->toArray())
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
                'id',
                'endpoint',
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
        'builder' => fn () => [fn ($builder) => $builder, Builder::class],
        'query' => fn () => [fn ($query) => $query, Builder::class],
        'q' => fn () => [fn ($q) => $q, Builder::class],
        'collection' => fn () => [fn ($collection) => $collection, Collection::class],
        'records' => fn () => [fn ($records) => $records, Collection::class],
        'batch' => fn () => [fn ($batch) => $batch, UserBatch::class],
    ]);

    it('typed dependencies', function ($closure, $class) {
        expect($this->batch->evaluate($closure))->toBeInstanceOf($class);
    })->with([
        'model' => fn () => [fn (Model $arg) => $arg, User::class],
        'user' => fn () => [fn (User $arg) => $arg, User::class],
        'builder' => fn () => [fn (Builder $arg) => $arg, Builder::class],
        'collection' => fn () => [fn (Collection $arg) => $arg, Collection::class],
        'batch' => fn () => [fn (Batch $arg) => $arg, Batch::class],
    ]);
});
