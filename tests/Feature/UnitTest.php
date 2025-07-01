<?php

declare(strict_types=1);

use Honed\Action\Unit;
use Illuminate\Contracts\Database\Eloquent\Builder as BuilderContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Workbench\App\Batches\UserBatch;
use Workbench\App\Models\User;

beforeEach(function () {
    $this->unit = (new Unit())->for(User::class);
});

it('finds primitive', function () {
    expect(Unit::find($this->unit->getRouteKey()))
        ->toBeNull();

    expect(Unit::find(UserBatch::make()->getRouteKey()))
        ->toBeInstanceOf(UserBatch::class);
});

it('resolves route binding', function () {
    expect($this->unit)
        ->getRouteKeyName()->toBe('unit')
        ->getRouteKey()->toBe(Unit::encode($this->unit::class));

    expect($this->unit)
        ->resolveRouteBinding($this->unit->getRouteKey())
        ->toBeNull();

    $actions = UserBatch::make();

    expect($actions)
        ->resolveRouteBinding($actions->getRouteKey())
        ->toBeInstanceOf(UserBatch::class);

    expect($actions)
        ->resolveChildRouteBinding(UserBatch::class, $actions->getRouteKey())
        ->toBeInstanceOf(UserBatch::class);
});

it('has array representation', function () {
    expect($this->unit->toArray())
        ->toBeArray()
        ->toHaveKeys([
            'inline',
            'bulk',
            'page',
        ]);
});

describe('evaluation', function () {
    it('named dependencies', function ($closure, $class) {
        expect($this->unit->evaluate($closure))->toBeInstanceOf($class);
    })->with([
        'builder' => fn () => [fn ($builder) => $builder, Builder::class],
        'query' => fn () => [fn ($query) => $query, Builder::class],
        'q' => fn () => [fn ($q) => $q, Builder::class],
        'collection' => fn () => [fn ($collection) => $collection, Collection::class],
        'records' => fn () => [fn ($records) => $records, Collection::class],
    ]);

    it('typed dependencies', function ($closure, $class) {
        expect($this->unit->evaluate($closure))->toBeInstanceOf($class);
    })->with([
        'builder' => fn () => [fn (Builder $arg) => $arg, Builder::class],
        'builder contract' => fn () => [fn (BuilderContract $arg) => $arg, Builder::class],
        'collection' => fn () => [fn (Collection $arg) => $arg, Collection::class],
    ]);
});
