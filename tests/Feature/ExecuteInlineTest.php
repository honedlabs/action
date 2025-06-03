<?php

declare(strict_types=1);

use Honed\Action\InlineAction;
use Honed\Core\Parameters;
use Illuminate\Http\RedirectResponse;
use Workbench\App\Actions\DestroyUser;
use Workbench\App\Actions\Inline\DestroyAction;
use Workbench\App\Models\User;

beforeEach(function () {
    $this->test = InlineAction::make('test');
    $this->user = User::factory()->create();
});

test('not without action', function () {
    expect($this->test->execute($this->user))
        ->toBeNull();

    $this->assertDatabaseHas('users', [
        'id' => $this->user->id,
    ]);
});

test('with callback', function () {
    $this->test->action(function (User $user) {
        $user->update(['name' => 'test']);

        return inertia('Users/Show', [
            'user' => $user,
        ]);
    });

    expect($this->test->execute($this->user))
        ->toBeInstanceOf(Inertia\Response::class);

    $this->assertDatabaseHas('users', [
        'id' => $this->user->id,
        'name' => 'test',
    ]);
});

test('with handler', function () {
    $action = new DestroyAction();

    expect($action)
        ->getName()->toBe('destroy')
        ->getLabel(...Parameters::model($this->user))->toBe('Destroy '.$this->user->name)
        ->getType()->toBe('inline')
        ->isActionable()->toBeTrue();

    $action->execute($this->user);

    $this->assertDatabaseMissing('users', [
        'id' => $this->user->id,
    ]);
});

test('with class-string action', function () {
    expect($this->test->action(DestroyUser::class))
        ->execute($this->user)
        ->toBeInstanceOf(RedirectResponse::class);

    $this->assertDatabaseMissing('users', [
        'id' => $this->user->id,
    ]);
});
