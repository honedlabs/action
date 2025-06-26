<?php

declare(strict_types=1);

use Honed\Action\Contracts\Action;
use Honed\Action\Operations\Concerns\HasAction;
use Honed\Action\Operations\InlineOperation;
use Workbench\App\Actions\User\DestroyUser;

beforeEach(function () {
    $this->operation = InlineOperation::make('test');
});

it('has action', function () {
    expect($this->operation)
        ->hasAction()->toBeFalse()
        ->getAction()->toBeNull()
        ->action(fn () => 'test')
        ->hasAction()->toBeTrue()
        ->getAction()->toBeInstanceOf(Closure::class);
});

it('has parameters', function () {
    expect($this->operation)
        ->getParameters()->toEqual([])
        ->parameters([
            'name' => 'test',
        ])->toBe($this->operation)
        ->getParameters()->toEqual(['name' => 'test']);
});

it('has handler', function () {
    expect($this->operation)
        ->getHandler()->toBeNull()
        ->action(fn () => 'test')->toBe($this->operation)
        ->getHandler()->toBeInstanceOf(Closure::class)
        ->action(DestroyUser::class)->toBe($this->operation)
        ->getHandler()->toBeInstanceOf(Closure::class);
});

it('has actionable', function () {
    $test = new class() implements Action
    {
        use HasAction;

        public function handle()
        {
            return 'test';
        }
    };

    expect($test)
        ->hasAction()->toBeTrue()
        ->getAction()->toBeNull()
        ->action(fn () => 'test')
        ->hasAction()->toBeTrue()
        ->getHandler()->toBeInstanceOf(Closure::class);
});
