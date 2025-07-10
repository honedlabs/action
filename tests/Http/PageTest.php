<?php

declare(strict_types=1);

namespace Tests\Pest\Handler;

use Workbench\App\Batches\UserBatch;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\get;
use function Pest\Laravel\patch;
use function Pest\Laravel\post;

beforeEach(function () {
    $this->batch = UserBatch::make();

    $this->batch->define();
});

it('handles a url', function () {
    get(route(config('action.name'), [$this->batch, 'create']))
        ->assertRedirect(route('users.create'));
});

it('handles an action', function () {
    post(route(config('action.name'), [$this->batch, 'create.name']))
        ->assertRedirect();

    assertDatabaseHas('users', [
        'name' => 'name',
    ]);
});

it('returns 403 if the action is not allowed', function () {
    post(route(config('action.name'), [$this->batch, 'create.description']))
        ->assertForbidden();
});

it('returns 404 if the action is not found', function () {
    post(route(config('action.name'), [$this->batch, 'missing']))
        ->assertNotFound();
});

it('returns 405 if the method is not supported', function () {
    patch(route(config('action.name'), [$this->batch, 'create.name']))
        ->assertMethodNotAllowed();
});

it('returns 429 if the rate limit is exceeded', function () {
    post(route(config('action.name'), [$this->batch, 'create.name']))
        ->assertRedirect();

    assertDatabaseHas('users', [
        'name' => 'name',
    ]);

    post(route(config('action.name'), [$this->batch, 'create.name']))
        ->assertStatus(429);
});
