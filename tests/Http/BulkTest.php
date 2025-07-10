<?php

declare(strict_types=1);

use Workbench\App\Batches\UserBatch;
use Workbench\App\Models\User;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\patch;
use function Pest\Laravel\post;

beforeEach(function () {
    User::factory()->count(10)->create();

    $this->batch = UserBatch::make();

    $this->batch->define();
});

it('handles an action', function () {
    patch(route(config('action.name'), [$this->batch, 'bulk-name']), [
        'all' => true,
        'except' => [],
        'only' => [],
    ])->assertRedirect();

    foreach (User::all() as $user) {
        assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'test',
        ]);
    }
});

it('returns 403 if the action is not allowed', function () {
    post(route(config('action.name'), [$this->batch, 'bulk-description']), [
        'all' => true,
        'except' => [],
        'only' => [],
    ])->assertForbidden();

    foreach (User::all() as $user) {
        assertDatabaseMissing('users', [
            'id' => $user->id,
            'description' => 'test',
        ]);
    }
});

it('returns 404 if the action is not found', function () {
    post(route(config('action.name'), [$this->batch, 'missing']), [
        'all' => true,
        'except' => [],
        'only' => [],
    ])->assertNotFound();
});

it('returns 405 if the method is not supported', function () {
    post(route(config('action.name'), [$this->batch, 'bulk-name']), [
        'all' => true,
        'except' => [],
        'only' => [],
    ])->assertMethodNotAllowed();

    foreach (User::all() as $user) {
        assertDatabaseMissing('users', [
            'id' => $user->id,
            'name' => 'test',
        ]);
    }
});

it('returns 429 if the rate limit is exceeded', function () {
    patch(route(config('action.name'), [$this->batch, 'bulk-name']), [
        'all' => true,
        'except' => [],
        'only' => [],
    ])->assertRedirect();

    foreach (User::all() as $user) {
        assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'test',
        ]);
    }

    patch(route(config('action.name'), [$this->batch, 'bulk-name']), [
        'all' => true,
        'except' => [],
        'only' => [],
    ])->assertStatus(429);
});

it('validates all', function () {
    patch(route(config('action.name'), [$this->batch, 'bulk-name']), [
        'only' => [],
        'except' => [],
    ])->assertInvalid([
        'all' => 'required',
    ]);
});

it('validates only', function () {
    patch(route(config('action.name'), [$this->batch, 'bulk-name']), [
        'all' => false,
        'only' => 'missing',
        'except' => [],
    ])->assertInvalid([
        'only' => 'array',
    ]);
});

it('validates except', function () {
    patch(route(config('action.name'), [$this->batch, 'bulk-name']), [
        'all' => false,
        'only' => [],
        'except' => 'missing',
    ])->assertInvalid([
        'except' => 'array',
    ]);
});

it('validates inputs', function () {
    patch(route(config('action.name'), [$this->batch, 'bulk-name']), [
        'all' => false,
        'only' => [['missing']],
        'except' => [['missing']],
    ])->assertInvalid(['only', 'except']);
});

it('applies only to selected records', function () {
    patch(route(config('action.name'), [$this->batch, 'bulk-name']), [
        'all' => false,
        'only' => [1, 2],
        'except' => [],
    ])->assertRedirect();

    foreach (User::query()->whereIn('id', [1, 2])->get() as $user) {
        assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'test',
        ]);
    }

    foreach (User::query()->whereNotIn('id', [1, 2])->get() as $user) {
        assertDatabaseMissing('users', [
            'id' => $user->id,
            'name' => 'test',
        ]);
    }
});

it('applies except to all records', function () {
    patch(route(config('action.name'), [$this->batch, 'bulk-name']), [
        'all' => true,
        'only' => [],
        'except' => [1, 2],
    ])->assertRedirect();

    foreach (User::query()->whereIn('id', [1, 2])->get() as $user) {
        assertDatabaseMissing('users', [
            'id' => $user->id,
            'name' => 'test',
        ]);
    }

    foreach (User::query()->whereNotIn('id', [1, 2])->get() as $user) {
        assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'test',
        ]);
    }
});

it('affects no records', function () {
    patch(route(config('action.name'), [$this->batch, 'bulk-name']), [
        'all' => false,
        'only' => [],
        'except' => [],
    ])->assertRedirect();

    assertDatabaseMissing('users', [
        'name' => 'test',
    ]);
});

it('handles a chunked operation', function () {
    post(route(config('action.name'), [$this->batch, 'chunk']), [
        'all' => true,
        'only' => [],
        'except' => [],
    ])->assertRedirect();

    foreach (User::all() as $user) {
        assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'chunk',
        ]);
    }
});

it('handles a chunked by id operation', function () {
    post(route(config('action.name'), [$this->batch, 'chunk-id']), [
        'all' => true,
        'only' => [],
        'except' => [],
    ])->assertRedirect();

    foreach (User::all() as $user) {
        assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'chunk.id',
        ]);
    }
});
