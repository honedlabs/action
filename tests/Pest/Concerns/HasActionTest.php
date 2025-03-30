<?php

declare(strict_types=1);

use Honed\Action\Concerns\HasAction;
use Honed\Action\Contracts\Actionable;
use Honed\Action\Tests\Fixtures\DestroyAction;

beforeEach(function () {
    $this->test = new class {
        use HasAction;
    };
});

it('has action', function () {
    expect($this->test)
        ->hasAction()->toBeFalse()
        ->getAction()->toBeNull()
        ->action(fn () => 'test')
        ->hasAction()->toBeTrue()
        ->getAction()->toBeInstanceOf(\Closure::class);
});

it('has parameters', function () {
    expect($this->test)
        ->getParameters()->toEqual([])
        ->parameters([
            'name' => 'test',
        ])->toBe($this->test)
        ->getParameters()->toEqual(['name' => 'test']);
});

it('has handler', function () {
    expect($this->test)
        ->getHandler()->toBeNull()
        ->action(fn () => 'test')->toBe($this->test)
        ->getHandler()->toBeInstanceOf(\Closure::class)
        ->action(DestroyAction::class)->toBe($this->test)
        ->getHandler()->toBeInstanceOf(\Closure::class);
});

it('has actionable', function () {
    $test = new class implements Actionable {
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
        ->getHandler()->toBeInstanceOf(\Closure::class);
});