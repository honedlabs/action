<?php

declare(strict_types=1);

use Honed\Action\Confirm;
use Illuminate\Database\Eloquent\Model;
use Workbench\App\Models\User;

beforeEach(function () {
    $this->confirm = Confirm::make();
});

it('has title', function () {
    expect($this->confirm)
        ->getTitle()->toBe(Confirm::TITLE)
        ->title('title')->toBe($this->confirm)
        ->getTitle()->toBe('title');
});

it('has description', function () {
    expect($this->confirm)
        ->getDescription()->toBe(Confirm::DESCRIPTION)
        ->description('description')->toBe($this->confirm)
        ->getDescription()->toBe('description');
});

it('has dismiss', function () {
    expect($this->confirm)
        ->getDismiss()->toBe(Confirm::DISMISS)
        ->dismiss('Back')->toBe($this->confirm)
        ->getDismiss()->toBe('Back');
});

it('has submit', function () {
    expect($this->confirm)
        ->getSubmit()->toBe(Confirm::SUBMIT)
        ->submit('Accept')->toBe($this->confirm)
        ->getSubmit()->toBe('Accept');
});

it('has intent', function () {
    expect($this->confirm)
        ->getIntent()->toBeNull()
        ->intent('danger')->toBe($this->confirm)
        ->getIntent()->toBe('danger')
        ->constructive()->toBe($this->confirm)
        ->getIntent()->toBe(Confirm::CONSTRUCTIVE)
        ->destructive()->toBe($this->confirm)
        ->getIntent()->toBe(Confirm::DESTRUCTIVE)
        ->informative()->toBe($this->confirm)
        ->getIntent()->toBe(Confirm::INFORMATIVE);
});

it('has array representation', function () {
    expect($this->confirm->toArray())
        ->toBeArray()
        ->toEqual([
            'title' => Confirm::TITLE,
            'description' => Confirm::DESCRIPTION,
            'submit' => Confirm::SUBMIT,
            'dismiss' => Confirm::DISMISS,
        ]);
});

it('resolves to array', function () {
    $user = User::factory()->create();

    $confirm = Confirm::make(
        fn (User $p) => $p->name,
        fn (User $p) => \sprintf('Are you sure you want to delete %s?', $p->name)
    );

    expect($confirm->record($user)->toArray())
        ->toEqual([
            'title' => $user->name,
            'description' => \sprintf('Are you sure you want to delete %s?', $user->name),
            'dismiss' => 'Cancel',
            'submit' => 'Confirm',
        ]);
});

describe('evaluation', function () {
    beforeEach(function () {
        $this->confirm = $this->confirm->record(User::factory()->create());
    });

    it('has named dependencies', function ($closure, $class) {
        expect($this->confirm->evaluate($closure))->toBeInstanceOf($class);
    })->with([
        fn () => [fn ($row) => $row, User::class],
        fn () => [fn ($record) => $record, User::class],
    ]);

    it('has typed dependencies', function ($closure, $class) {
        expect($this->confirm->evaluate($closure))->toBeInstanceOf($class);
    })->with([
        fn () => [fn (Model $arg) => $arg, User::class],
        fn () => [fn (User $arg) => $arg, User::class],
    ]);
});
