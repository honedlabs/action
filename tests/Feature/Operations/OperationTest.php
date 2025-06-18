<?php

declare(strict_types=1);

use Honed\Action\Action;
use Honed\Action\Confirm;
use Honed\Action\Operations\InlineOperation;
use Honed\Core\Parameters;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Request;
use Workbench\App\Actions\Inline\DestroyAction;
use Workbench\App\Models\User;

beforeEach(function () {
    // Using inline action for testing base class
    $this->action = InlineOperation::make('test');
});

it('has implicit route bindings', function () {
    $user = User::factory()->create();

    [$named, $typed] = Parameters::model($user);

    $this->action->route('users.show', '{user}');

    expect($this->action->toArray($named, $typed))
        ->toHaveKey('route')
        ->{'route'}
        ->scoped(fn ($route) => $route
            ->toHaveKey('url')
            ->{'url'}->toBe(route('users.show', $user))
        );
});

it('has array representation', function () {
    expect($this->action->toArray())
        ->toBeArray()
        ->toHaveKeys([
            'name',
            'label',
            'type',
            'icon',
            'extra',
            'actionable',
            'confirm',
            'route',
        ]);
});

it('has array representation with route', function () {
    expect($this->action->route('users.index')->toArray())
        ->toBeArray()
        ->toEqual([
            'name' => 'test',
            'label' => 'Test',
            'type' => 'inline',
            'icon' => null,
            'extra' => null,
            'actionable' => false,
            'confirm' => null,
            'default' => false,
            'route' => [
                'url' => route('users.index'),
                'method' => Request::METHOD_GET,
                'external' => false,
            ],
        ]);
});

it('resolves to array', function () {
    $user = User::factory()->create();

    [$named, $typed] = Parameters::model($user);

    expect((new DestroyAction())->toArray($named, $typed))
        ->toEqual([
            'name' => 'destroy',
            'label' => 'Destroy '.$user->name,
            'type' => 'inline',
            'icon' => null,
            'extra' => null,
            'actionable' => true,
            'confirm' => null,
            'default' => false,
            'route' => null,
        ]);
});

it('evaluates names', function () {
    expect($this->action->evaluate(fn ($confirm) => $confirm->title('test')))
        ->toBeInstanceOf(Confirm::class)
        ->getTitle()->toBe('test');

    $name = Str::random();
    expect($this->action->parameters(['name' => $name])
        ->evaluate(fn ($name) => $name))->toBe($name);
});

it('evaluates types', function () {
    expect($this->action->evaluate(fn (Confirm $confirm) => $confirm->title('test')))
        ->toBeInstanceOf(Confirm::class)
        ->getTitle()->toBe('test');

    // Dependency injection
    expect($this->action->evaluate(fn (User $user) => $user))
        ->toBeInstanceOf(User::class);
});
