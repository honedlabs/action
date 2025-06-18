<?php

declare(strict_types=1);

namespace Tests\Pest\Handler;

use Honed\Action\Testing\PageRequest;
use Workbench\App\Batches\UserBatch;

use function Pest\Laravel\post;

beforeEach(function () {
    $this->request = PageRequest::fake()
        ->for(UserBatch::class)
        ->fill();
});

it('executes the action', function () {
    $data = $this->request
        ->name('create.name')
        ->getData();

    $response = post(route('actions'), $data);

    $response->assertRedirect();

    $this->assertDatabaseHas('users', [
        'name' => 'name',
    ]);
});

it('is 404 for no name match', function () {
    $data = $this->request
        ->name('missing')
        ->getData();

    $response = post(route('actions'), $data);

    $response->assertNotFound();
});

it('is 403 if the action is not allowed', function () {
    $data = $this->request
        ->name('create.description')
        ->getData();

    $response = post(route('actions'), $data);

    $response->assertForbidden();
});

it('does not execute route actions', function () {
    $data = $this->request
        ->name('create')
        ->getData();

    $response = post(route('actions'), $data);

    $response->assertRedirect();
});
