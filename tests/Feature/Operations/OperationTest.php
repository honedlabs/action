<?php

declare(strict_types=1);

use Honed\Action\Action;
use Honed\Action\Confirm;
use Honed\Action\Operations\InlineOperation;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Request;
use Workbench\App\Models\User;
use Workbench\App\Operations\DestroyOperation;

beforeEach(function () {
    // Using inline action for testing base class
    $this->operation = InlineOperation::make('test');
});

it('has implicit route bindings', function () {
    $user = User::factory()->create();

    $this->operation->route('users.show', '{user}');

    expect($this->operation->record($user)->toArray())
        ->toHaveKey('route')
        ->{'route'}
        ->scoped(fn ($route) => $route
            ->toHaveKey('url')
            ->{'url'}->toBe(route('users.show', $user))
        );
});

it('has array representation', function () {
    expect($this->operation->toArray())
        ->toBeArray()
        ->toHaveKeys([
            'name',
            'label',
            'type',
            'icon',
            'extra',
            'action',
            'confirm',
            'route',
        ]);
});

it('has array representation with route', function () {
    expect($this->operation->route('users.index')->toArray())
        ->toBeArray()
        ->toEqual([
            'name' => 'test',
            'label' => 'Test',
            'type' => 'inline',
            'icon' => null,
            'extra' => null,
            'action' => false,
            'confirm' => null,
            'default' => false,
            'route' => [
                'url' => route('users.index'),
                'method' => Request::METHOD_GET,
                'external' => false,
                'newTab' => false,
            ],
        ]);
});

it('resolves to array', function () {
    $user = User::factory()->create();

    expect((new DestroyOperation())->record($user)->toArray())
        ->toEqual([
            'name' => 'destroy',
            'label' => 'Destroy '.$user->name,
            'type' => 'inline',
            'icon' => null,
            'extra' => null,
            'action' => true,
            'confirm' => null,
            'default' => false,
            'route' => null,
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
    ]);
});
