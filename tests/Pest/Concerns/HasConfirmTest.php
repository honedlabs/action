<?php

declare(strict_types=1);

use Honed\Action\Confirm;
use Honed\Action\InlineAction;
use Honed\Action\Tests\Stubs\Product;

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
    $product = product();

    $test = $this->test->confirm(fn (Confirm $confirm) => $confirm
        ->title(fn (Product $product) => \sprintf('Delete %s', $product->name))
        ->description(fn (Product $product) => \sprintf('Are you sure you want to delete %s?', $product->name))
        ->submit('Delete')
        ->destructive()
    );

    expect($test->getConfirm()->resolveToArray(...params($product)))
        ->toEqual([
            'title' => \sprintf('Delete %s', $product->name),
            'description' => \sprintf('Are you sure you want to delete %s?', $product->name),
            'intent' => Confirm::DESTRUCTIVE,
            'submit' => 'Delete',
            'dismiss' => 'Cancel',
        ]);
});
