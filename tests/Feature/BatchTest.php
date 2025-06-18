<?php

declare(strict_types=1);

use Honed\Action\Batch;
use Honed\Action\Operations\PageOperation;
use Honed\Action\Testing\RequestFactory;
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

    expect(UserBatch::make())
        ->handle($request)
        ->toBeInstanceOf(RedirectResponse::class);

    expect(User::query()->count())->toBe(1);
});

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

it('resolves action group', function () {
    UserBatch::guessBatchNamesUsing(function ($class) {
        return Str::of($class)
            ->afterLast('\\')
            ->prepend('Workbench\\App\\Batchs\\')
            ->append('Actions')
            ->value();
    });

    expect(UserBatch::resolveBatchName(User::class))
        ->toBe(UserBatch::class);

    expect(UserBatch::actionGroupForModel(User::class))
        ->toBeInstanceOf(UserBatch::class);

    UserBatch::flushState();
});

it('uses namespace', function () {
    Batch::useNamespace('');

    expect(UserBatch::resolveBatchName(User::class))
        ->toBe(Str::of(UserBatch::class)
            ->afterLast('\\')
            ->prepend('Models\\')
            ->value()
        );

    UserBatch::flushState();
});

it('has array representation', function () {
    expect($this->group->toArray())
        ->toBeArray()
        ->toHaveCount(3)
        ->toHaveKeys(['inline', 'bulk', 'page']);
});

it('has array representation with server actions', function () {
    expect(UserBatch::make()->for(User::factory()->create())->toArray())
        ->toBeArray()
        ->toHaveCount(5)
        ->toHaveKeys(['id', 'endpoint', 'inline', 'bulk', 'page']);

    expect(UserBatch::make()->for(User::factory()->create())->executes(false)->toArray())
        ->toHaveCount(3)
        ->toHaveKeys(['inline', 'bulk', 'page']);
});

it('has array representation with model', function () {
    $user = User::factory()->create();

    expect(UserBatch::make()->for($user)->toArray())
        ->toBeArray()
        ->toHaveCount(5)
        ->toHaveKeys(['inline', 'bulk', 'page', 'id', 'endpoint']);
});
