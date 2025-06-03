<?php

declare(strict_types=1);

use Honed\Action\Confirm;
use Honed\Action\InlineAction;
use Honed\Core\Parameters;
use Workbench\App\Models\User;

beforeEach(function () {
    $this->test = InlineAction::make('test');
});

it('sets with instance', function () {
    expect($this->test->confirm(Confirm::make('name', 'description')->constructive()))
        ->toBe($this->test)
        ->hasConfirm()->toBeTrue()
        ->getConfirm()->scoped(fn ($confirm) => $confirm
        ->getTitle()->toBe('name')
        ->getDescription()->toBe('description')
        ->getIntent()->toBe(Confirm::CONSTRUCTIVE)
        );
});

it('sets with self-call', function () {
    expect($this->test->confirm(fn (Confirm $confirm) => $confirm
        ->title('name')
        ->description('description')
        ->submit('Accept'))
    )->toBe($this->test)
        ->hasConfirm()->toBeTrue()
        ->getConfirm()->scoped(fn ($confirm) => $confirm
        ->getTitle()->toBe('name')
        ->getDescription()->toBe('description')
        ->getSubmit()->toBe('Accept')
        );
});

it('sets with strings', function () {
    expect($this->test->confirm('name', 'description'))
        ->toBe($this->test)
        ->hasConfirm()->toBeTrue()
        ->getConfirm()->scoped(fn ($confirm) => $confirm
        ->getTitle()->toBe('name')
        ->getDescription()->toBe('description')
        );
});

it('resolves to array', function () {
    $user = User::factory()->create();

    $test = $this->test->confirm(fn (Confirm $confirm) => $confirm
        ->title(fn (User $user) => \sprintf('Delete %s', $user->name))
        ->description(fn (User $user) => \sprintf('Are you sure you want to delete %s?', $user->name))
        ->submit('Delete')
        ->destructive()
    );

    [$named, $typed] = Parameters::model($user);

    expect($test->getConfirm()->toArray($named, $typed))
        ->toEqual([
            'title' => \sprintf('Delete %s', $user->name),
            'description' => \sprintf('Are you sure you want to delete %s?', $user->name),
            'intent' => Confirm::DESTRUCTIVE,
            'submit' => 'Delete',
            'dismiss' => 'Cancel',
        ]);
});
