<?php

declare(strict_types=1);

use Honed\Action\Confirm;
use Honed\Action\Operations\InlineOperation;
use Workbench\App\Models\User;

beforeEach(function () {
    $this->operation = InlineOperation::make('test');
});

it('sets with instance', function () {
    expect($this->operation->confirmable(Confirm::make('name', 'description')->constructive()))
        ->toBe($this->operation)
        ->isConfirmable()->toBeTrue()
        ->isNotConfirmable()->toBeFalse()
        ->getConfirm()
        ->scoped(fn ($confirm) => $confirm
            ->getTitle()->toBe('name')
            ->getDescription()->toBe('description')
            ->getIntent()->toBe(Confirm::CONSTRUCTIVE)
        );
});

it('sets with defaults', function () {
    expect($this->operation->confirmable())
        ->toBe($this->operation)
        ->isConfirmable()->toBeTrue()
        ->isNotConfirmable()->toBeFalse()
        ->getConfirm()
        ->scoped(fn ($confirm) => $confirm
            ->getTitle()->toBe(Confirm::TITLE)
            ->getDescription()->toBe(Confirm::DESCRIPTION)
            ->getIntent()->toBeNull()
            ->getSubmit()->toBe(Confirm::SUBMIT)
            ->getDismiss()->toBe(Confirm::DISMISS)
        );
});

it('sets as not confirmable', function () {
    expect($this->operation)
        ->confirmable()->toBe($this->operation)
        ->getConfirm()->toBeInstanceOf(Confirm::class)
        ->isConfirmable()->toBeTrue()
        ->notConfirmable()->toBe($this->operation)
        ->getConfirm()->toBeNull()
        ->isNotConfirmable()->toBeTrue();
});

it('sets with self-call', function () {
    expect($this->operation->confirmable(fn (Confirm $confirm) => $confirm
        ->title('name')
        ->description('description')
        ->submit('Accept'))
    )->toBe($this->operation)
        ->isConfirmable()->toBeTrue()
        ->isNotConfirmable()->toBeFalse()
        ->getConfirm()
        ->scoped(fn ($confirm) => $confirm
            ->getTitle()->toBe('name')
            ->getDescription()->toBe('description')
            ->getSubmit()->toBe('Accept')
        );
});

it('sets with strings', function () {
    expect($this->operation->confirmable('name', 'description'))
        ->toBe($this->operation)
        ->isConfirmable()->toBeTrue()
        ->isNotConfirmable()->toBeFalse()
        ->getConfirm()
        ->scoped(fn ($confirm) => $confirm
            ->getTitle()->toBe('name')
            ->getDescription()->toBe('description')
        );
});

it('resolves to array', function () {
    $user = User::factory()->create();

    $test = $this->operation->confirmable(fn (Confirm $confirm) => $confirm
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
