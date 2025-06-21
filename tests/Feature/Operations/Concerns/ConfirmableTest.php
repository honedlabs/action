<?php

declare(strict_types=1);

use Honed\Action\Confirm;
use Honed\Action\Operations\InlineOperation;
use Workbench\App\Models\User;

beforeEach(function () {
    $this->test = InlineOperation::make('test');
});

it('sets with instance', function () {
    expect($this->test->confirmable(Confirm::make('name', 'description')->constructive()))
        ->toBe($this->test)
        ->getConfirm()
        ->scoped(fn ($confirm) => $confirm
            ->getTitle()->toBe('name')
            ->getDescription()->toBe('description')
            ->getIntent()->toBe(Confirm::CONSTRUCTIVE)
        );
});

it('sets with self-call', function () {
    expect($this->test->confirmable(fn (Confirm $confirm) => $confirm
        ->title('name')
        ->description('description')
        ->submit('Accept'))
    )->toBe($this->test)
        ->getConfirm()
        ->scoped(fn ($confirm) => $confirm
            ->getTitle()->toBe('name')
            ->getDescription()->toBe('description')
            ->getSubmit()->toBe('Accept')
        );
});

it('sets with strings', function () {
    expect($this->test->confirmable('name', 'description'))
        ->toBe($this->test)
        ->getConfirm()
        ->scoped(fn ($confirm) => $confirm
            ->getTitle()->toBe('name')
            ->getDescription()->toBe('description')
        );
});

it('resolves to array', function () {
    $user = User::factory()->create();

    $test = $this->test->confirmable(fn (Confirm $confirm) => $confirm
        ->title(fn (User $user) => \sprintf('Delete %s', $user->name))
        ->description(fn (User $user) => \sprintf('Are you sure you want to delete %s?', $user->name))
        ->submit('Delete')
        ->destructive()
    );

    expect($test->getConfirm()->record($user)->toArray())
        ->toEqual([
            'title' => \sprintf('Delete %s', $user->name),
            'description' => \sprintf('Are you sure you want to delete %s?', $user->name),
            'intent' => Confirm::DESTRUCTIVE,
            'submit' => 'Delete',
            'dismiss' => 'Cancel',
        ]);
});
