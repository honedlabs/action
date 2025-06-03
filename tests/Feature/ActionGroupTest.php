<?php

declare(strict_types=1);

use Honed\Action\ActionGroup;
use Honed\Action\PageAction;
use Honed\Action\Testing\RequestFactory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Workbench\App\ActionGroups\UserActions;
use Workbench\App\Models\User;

beforeEach(function () {
    $this->group = ActionGroup::make(PageAction::make('create'));
});

it('has model', function () {
    expect($this->group)
        ->getModel()->toBeNull()
        ->for(User::factory()->create())->toBe($this->group)
        ->getModel()->toBeInstanceOf(User::class);
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

it('handles requests with model', function () {

    $request = RequestFactory::page()
        ->fill()
        ->name('create.name')
        ->validate();

    expect(User::query()->count())->toBe(0);

    expect(UserActions::make())
        ->handle($request)
        ->toBeInstanceOf(RedirectResponse::class);

    expect(User::query()->count())->toBe(1);
});

it('resolves route binding', function () {
    expect($this->group)
        ->resolveRouteBinding($this->group->getRouteKey())
        ->toBeNull();

    $actions = UserActions::make();

    expect($actions)
        ->resolveRouteBinding($actions->getRouteKey())
        ->toBeInstanceOf(UserActions::class);

    expect($actions)
        ->resolveChildRouteBinding(UserActions::class, $actions->getRouteKey())
        ->toBeInstanceOf(UserActions::class);
});

it('resolves action group', function () {
    UserActions::guessActionGroupNamesUsing(function ($class) {
        return Str::of($class)
            ->afterLast('\\')
            ->prepend('Workbench\\App\\ActionGroups\\')
            ->append('Actions')
            ->value();
    });

    expect(UserActions::resolveActionGroupName(User::class))
        ->toBe(UserActions::class);

    expect(UserActions::actionGroupForModel(User::class))
        ->toBeInstanceOf(UserActions::class);

    UserActions::flushState();
});

it('uses namespace', function () {
    ActionGroup::useNamespace('');

    expect(UserActions::resolveActionGroupName(User::class))
        ->toBe(Str::of(UserActions::class)
            ->afterLast('\\')
            ->prepend('Models\\')
            ->value()
        );

    UserActions::flushState();
});

it('has array representation', function () {
    expect($this->group->toArray())
        ->toBeArray()
        ->toHaveCount(3)
        ->toHaveKeys(['inline', 'bulk', 'page']);
});

it('has array representation with server actions', function () {
    expect(UserActions::make()->for(User::factory()->create())->toArray())
        ->toBeArray()
        ->toHaveCount(5)
        ->toHaveKeys(['id', 'endpoint', 'inline', 'bulk', 'page']);

    expect(UserActions::make()->for(User::factory()->create())->executes(false)->toArray())
        ->toHaveCount(3)
        ->toHaveKeys(['inline', 'bulk', 'page']);
});

it('has array representation with model', function () {
    $user = User::factory()->create();

    expect(UserActions::make()->for($user)->toArray())
        ->toBeArray()
        ->toHaveCount(5)
        ->toHaveKeys(['inline', 'bulk', 'page', 'id', 'endpoint']);
});
