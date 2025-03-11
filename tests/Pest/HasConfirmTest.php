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
            ->getLabel()->toBe('name')
            ->getDescription()->toBe('description')
            ->getIntent()->toBe(Confirm::Constructive)
        );
});

it('sets with self-call', function () {
    expect($this->test->confirm(fn (Confirm $confirm) => $confirm
        ->label('name')
        ->description('description')
        ->submit('Accept'))
    )->toBe($this->test)
        ->hasConfirm()->toBeTrue()
        ->getConfirm()->scoped(fn ($confirm) => $confirm
            ->getLabel()->toBe('name')
            ->getDescription()->toBe('description')
            ->getSubmit()->toBe('Accept')
        );
});

it('sets with strings', function () {
    expect($this->test->confirm('name', 'description'))
        ->toBe($this->test)
        ->hasConfirm()->toBeTrue()
        ->getConfirm()->scoped(fn ($confirm) => $confirm
            ->getLabel()->toBe('name')
            ->getDescription()->toBe('description')
        );
});

it('resolves', function () {
    $product = product();

    $test = $this->test->confirm(fn (Confirm $confirm) => $confirm
        ->label(fn (Product $product) => \sprintf('Delete %s', $product->name))
        ->description(fn (Product $product) => \sprintf('Are you sure you want to delete %s?', $product->name))
        ->submit('Delete')
        ->destructive());

    expect($test)
        ->hasConfirm()->toBeTrue()
        ->resolveConfirm(...params($product))->toBeInstanceOf(InlineAction::class)
        ->getConfirm()->scoped(fn ($confirm) => $confirm
            ->getLabel()->toBe(\sprintf('Delete %s', $product->name))
            ->getDescription()->toBe(\sprintf('Are you sure you want to delete %s?', $product->name))
            ->getSubmit()->toBe('Delete')
            ->getIntent()->toBe(Confirm::Destructive)
        );
});
