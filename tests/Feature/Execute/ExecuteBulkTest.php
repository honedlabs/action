<?php

declare(strict_types=1);

use Honed\Action\Operations\BulkOperation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Workbench\App\Models\User;

beforeEach(function () {
    $this->action = BulkOperation::make('test');

    $this->user = User::factory()->create();
});

it('executes on builder', function () {
    $fn = fn (Builder $q) => $q->update(['name' => 'test']);

    $this->action->action($fn)->execute(User::query());

    $this->assertDatabaseHas('users', [
        'id' => $this->user->id,
        'name' => 'test',
    ]);
});

it('executes on model', function () {
    $fn = fn (User $u) => $u->update(['name' => 'test']);

    $this->action->action($fn)->execute($this->user);

    $this->assertDatabaseHas('users', [
        'id' => $this->user->id,
        'name' => 'test',
    ]);
});

it('executes on collection', function () {
    $fn = fn (Collection $u) => $u->each(fn (User $u) => $u->update(['name' => 'test']));

    $this->action->action($fn)->execute(User::query());

    $this->assertDatabaseHas('users', [
        'id' => $this->user->id,
        'name' => 'test',
    ]);
});

it('executes on chunked by id collection', function () {
    $this->action->chunksById();

    $fn = fn (Collection $u) => $u->each(fn (User $u) => $u->update(['name' => 'test']));

    $this->action->action($fn)->execute(User::query());

    $this->assertDatabaseHas('users', [
        'id' => $this->user->id,
        'name' => 'test',
    ]);
});

it('executes on chunked by id models', function () {
    $this->action->chunksById();

    $fn = fn (User $q) => $q->update(['name' => 'test']);

    $this->action->action($fn)->execute(User::query());

    $this->assertDatabaseHas('users', [
        'id' => $this->user->id,
        'name' => 'test',
    ]);
});

it('executes on chunked collection', function () {
    $fn = fn (Collection $u) => $u->each(fn (User $u) => $u->update(['name' => 'test']));

    $this->action->chunks();

    $fn = fn (Collection $u) => $u->each(fn (User $u) => $u->update(['name' => 'test']));

    $this->action->action($fn)->execute(User::query());

    $this->assertDatabaseHas('users', [
        'id' => $this->user->id,
        'name' => 'test',
    ]);
});

it('executes on chunked models', function () {
    $this->action->chunks();

    $fn = fn (User $q) => $q->update(['name' => 'test']);

    $this->action->action($fn)->execute(User::query());

    $this->assertDatabaseHas('users', [
        'id' => $this->user->id,
        'name' => 'test',
    ]);
});

it('modifies query', function () {
    User::factory()->count(10)->create();

    $modify = fn (Builder $q) => $q->where('id', '>', 5);
    $fn = fn (Collection $u) => $u->each(fn (User $u) => $u->update(['name' => 'test']));

    $this->action
        ->query($modify)
        ->action($fn)
        ->execute(User::query());

    // Users with id > 5 should have price = 0
    for ($i = 6; $i <= 10; $i++) {
        $this->assertDatabaseHas('users', [
            'id' => $i,
            'name' => 'test',
        ]);
    }

    // Users with id <= 5 should not have price = 0
    for ($i = 1; $i <= 5; $i++) {
        $this->assertDatabaseHas('users', [
            'id' => $i,
        ]);
        $this->assertDatabaseMissing('users', [
            'id' => $i,
            'name' => 'test',
        ]);
    }
});

it('errors if chunking with builder', function () {
    $fn = fn (Builder $q) => $q->update(['name' => 'test']);

    $this->action->action($fn)
        ->chunks()
        ->execute(User::query());

})->throws(RuntimeException::class);
