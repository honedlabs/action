<?php

declare(strict_types=1);

use Honed\Action\Action;
use Honed\Action\Confirm;
use Honed\Action\Operations\InlineOperation;
use Honed\Action\Operations\Operation;
use Illuminate\Support\Str;
use League\Uri\UriTemplate\Operator;
use Workbench\App\Models\User;
use Workbench\App\Operations\DestroyOperation;

beforeEach(function () {
    // Using inline action for testing base class
    $this->operation = InlineOperation::make('test');
});

it('has implicit route bindings', function () {
    $user = User::factory()->create();

    $this->operation->url('users.show', '{user}');

    expect($this->operation->record($user)->toArray())
        ->toHaveKey('href')
        ->{'href'}->toBe(route('users.show', $user));
});

it('is url routable', function () {
    expect($this->operation)
        ->getRouteKeyName()->toBe('operation')
        ->getRouteKey()->toBe('test')
        ->resolveRouteBinding('test')->toBe($this->operation)
        ->resolveChildRouteBinding(Operator::class, 'test')->toBe($this->operation);
});

it('has array representation', function () {
    expect($this->operation->toArray())
        ->toBeArray()
        ->toHaveKeys([
            'name',
            'label',
        ]);
});

it('has array representation with url', function () {
    expect($this->operation->url('users.index')->notInertia()->toArray())
        ->toBeArray()
        ->toEqual([
            'name' => 'test',
            'label' => 'Test',
            'default' => false,
            'type' => 'anchor',
            'href' => route('users.index'),
            'method' => 'get',
        ]);
});

it('resolves to array', function () {
    $user = User::factory()->create();

    expect((new DestroyOperation())->record($user)->toArray())
        ->toEqual([
            'name' => 'destroy',
            'label' => 'Destroy '.$user->name,
            'default' => false,
        ]);
});

it('evaluates names', function () {
    expect($this->operation->evaluate(fn ($confirm) => $confirm->title('test')))
        ->toBeInstanceOf(Confirm::class)
        ->getTitle()->toBe('test');

    $name = Str::random();
    expect($this->operation->parameters(['name' => $name])
        ->evaluate(fn ($name) => $name))->toBe($name);
});

describe('evaluation', function () {
    it('named dependencies', function ($closure, $class) {
        expect($this->operation->evaluate($closure))->toBeInstanceOf($class);
    })->with([
        fn () => [fn ($confirm) => $confirm, Confirm::class],
    ]);

    it('from parameters', function () {
        expect($this->operation)
            ->action(fn ($parameter) => $parameter, [
                'parameter' => 'parameter',
            ])->toBe($this->operation)
            ->evaluate(fn ($parameter) => $parameter)
            ->toBe('parameter');
    });

    it('typed dependencies', function ($closure, $class) {
        expect($this->operation->evaluate($closure))->toBeInstanceOf($class);
    })->with([
        fn () => [fn (Confirm $arg) => $arg, Confirm::class],
        fn () => [fn (Operation $arg) => $arg, Operation::class],
    ]);
});
