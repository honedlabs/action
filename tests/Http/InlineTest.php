<?php

declare(strict_types=1);

use Workbench\App\Batches\UserBatch;
use Workbench\App\Models\User;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\get;
use function Pest\Laravel\patch;
use function Pest\Laravel\post;

beforeEach(function () {
    $this->user = User::factory()->create();

    $this->batch = UserBatch::make();
});

it('handles a url', function () {
    get(route(config('action.name'), [$this->batch, 'show', 'id' => $this->user->id]))
        ->assertRedirect(route('users.show', $this->user->id));
});

it('handles an action', function () {
    patch(route(config('action.name'), [$this->batch, 'inline-name']), [
        'id' => $this->user->id,
    ])->assertRedirect(route('users.show', $this->user->id));

    $this->assertDatabaseHas('users', [
        'id' => $this->user->id,
        'name' => 'test',
    ]);
});

it('requires a record id', function () {
    patch(route(config('action.name'), [$this->batch, 'inline-name']))
        ->assertInvalid(['id' => 'required']);

    assertDatabaseMissing('users', [
        'id' => $this->user->id,
        'name' => 'test',
    ]);
});

it('requires a valid record id', function () {
    patch(route(config('action.name'), [$this->batch, 'inline-name']), [
        'id' => [1],
    ])->assertInvalid(['id']);

    assertDatabaseMissing('users', [
        'id' => $this->user->id,
        'name' => 'test',
    ]);
});

it('returns 403 if the action is not allowed', function () {
    post(route(config('action.name'), [$this->batch, 'inline-description']), [
        'id' => $this->user->id,
    ])->assertForbidden();

    assertDatabaseMissing('users', [
        'id' => $this->user->id,
        'name' => 'test',
    ]);
});

it('returns 404 if the action is not found', function () {
    post(route(config('action.name'), [$this->batch, 'missing']), [
        'id' => $this->user->id,
    ])->assertNotFound();

    assertDatabaseMissing('users', [
        'id' => $this->user->id,
        'name' => 'test',
    ]);
});

it('returns 405 if the method is not supported', function () {
    post(route(config('action.name'), [$this->batch, 'inline-name']), [
        'id' => $this->user->id,
    ])->assertMethodNotAllowed();

    assertDatabaseMissing('users', [
        'id' => $this->user->id,
        'name' => 'test',
    ]);
});

it('returns 429 if the rate limit is exceeded', function () {
    patch(route(config('action.name'), [$this->batch, 'inline-name']), [
        'id' => $this->user->id,
    ])->assertRedirect();

    assertDatabaseHas('users', [
        'id' => $this->user->id,
        'name' => 'test',
    ]);

    $this->user->refresh()->update(['name' => 'new']);

    patch(route(config('action.name'), [$this->batch, 'inline-name']), [
        'id' => $this->user->id,
    ])->assertStatus(429);

    assertDatabaseHas('users', [
        'id' => $this->user->id,
        'name' => 'new',
    ]);
});

it('returns 404 if the record is not found', function () {
    patch(route(config('action.name'), [$this->batch, 'inline-name']), [
        'id' => 2,
    ])->assertNotFound();

    assertDatabaseMissing('users', [
        'id' => 2,
        'name' => 'test',
    ]);
});
