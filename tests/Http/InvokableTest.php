<?php

declare(strict_types=1);

use Honed\Action\Testing\InlineRequest;
use Workbench\App\ActionGroups\UserActions;
use Workbench\App\Models\User;

use function Pest\Laravel\post;

beforeEach(function () {
    $this->user = User::factory()->create();

    $this->actions = UserActions::make();

    $this->request = InlineRequest::fake()
        ->for($this->actions)
        ->record($this->user->id)
        ->name('update.name')
        ->fill();
});

it('executes the action', function () {
    $data = $this->request->getData();

    $response = post(route('actions.invoke', $this->actions), $data);

    $response->assertRedirect();

    $this->assertDatabaseHas('users', [
        'id' => $this->user->id,
        'name' => 'test',
    ]);
});

it('does not execute non-existent action', function () {
    $key = UserActions::encode(User::class);

    $data = $this->request
        ->record($this->user->id)
        ->name('update.name')
        ->getData();

    $response = post(route('actions.invoke', $key), $data);

    $response->assertNotFound();
});

// it('does not execute for non-executable actions', function () {
//     $group = RouteUserActions::make();

//     $data = $this->request->for($group)->getData();

//     $response = post(route('actions.invoke', $group), $data);

//     $response->assertNotFound();
// });
