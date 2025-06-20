<?php

declare(strict_types=1);

use Honed\Action\Operations\InlineOperation;
use Illuminate\Database\Eloquent\Model;
use Workbench\App\Models\User;

beforeEach(function () {
    $this->operation = InlineOperation::make('test');
});

it('has inline type', function () {
    expect($this->operation)
        ->getType()->toBe(InlineOperation::INLINE)
        ->isInline()->toBeTrue();
});

it('has default', function () {
    expect($this->operation)
        ->isDefault()->toBeFalse()
        ->default()->toBe($this->operation)
        ->isDefault()->toBeTrue();
});

it('has array representation', function () {
    expect($this->operation->toArray())
        ->toBeArray()
        ->toHaveKey('default');
});

describe('evaluation', function () {
    beforeEach(function () {
        $this->operation = $this->operation->record(User::factory()->create());
    });

    it('named dependencies', function ($closure, $class) {
        expect($this->operation->evaluate($closure))->toBeInstanceOf($class);
    })->with([
        fn () => [fn ($row) => $row, User::class],
        fn () => [fn ($record) => $record, User::class],
    ]);

    it('typed dependencies', function ($closure, $class) {
        expect($this->operation->evaluate($closure))->toBeInstanceOf($class);
    })->with([
        fn () => [fn (Model $arg) => $arg, User::class],
        fn () => [fn (User $arg) => $arg, User::class],
    ]);
});
