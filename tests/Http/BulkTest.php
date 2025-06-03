<?php

declare(strict_types=1);

use Honed\Action\Testing\BulkRequest;
use Workbench\App\ActionGroups\UserActions;
use Workbench\App\Models\User;

use function Pest\Laravel\post;

beforeEach(function () {
    User::factory()->count(10)->create();

    $this->request = BulkRequest::make()
        ->fill()
        ->for(UserActions::class);
});

it('executes the action', function () {
    $data = $this->request
        ->all()
        ->name('update.description')
        ->getData();

    $response = post(route('actions'), $data);

    $response->assertRedirect();

    expect(User::all())
        ->each(fn ($user) => $user
            ->name->toBe('description')
        );
});

it('is 404 for no name match', function () {
    $data = $this->request
        ->all()
        ->name('missing')
        ->getData();

    $response = post(route('actions'), $data);

    $response->assertNotFound();
});

it('is 403 if the action is not allowed', function () {
    $data = $this->request
        ->all()
        ->name('update.name')
        ->getData();

    $response = post(route('actions'), $data);

    $response->assertForbidden();
});

it('does not mix action types', function () {
    $data = $this->request
        ->all()
        ->name('create.name')
        ->getData();

    $response = post(route('actions'), $data);

    $response->assertNotFound();
});

it('applies only to selected records', function () {
    $ids = [1, 2, 3, 4, 5];
    $data = $this->request
        ->only($ids)
        ->name('update.description')
        ->getData();

    $response = post(route('actions'), $data);

    $response->assertRedirect();

    expect(User::query()->whereIn('id', $ids)->get())
        ->each(fn ($user) => $user
            ->name->toBe('description')
        );

    expect(User::query()->whereNotIn('id', $ids)->get())
        ->each(fn ($user) => $user
            ->name->not->toBe('description')
        );

});

it('applies all excepted records', function () {
    $ids = [1, 2];

    $data = $this->request
        ->all()
        ->except($ids)
        ->name('update.description')
        ->getData();

    $response = post(route('actions'), $data);

    $response->assertRedirect();

    expect(User::query()->whereIn('id', $ids)->get())
        ->each(fn ($user) => $user
            ->name->not->toBe('description')
        );

    expect(User::query()->whereNotIn('id', $ids)->get())
        ->each(fn ($user) => $user
            ->name->toBe('description')
        );
});
