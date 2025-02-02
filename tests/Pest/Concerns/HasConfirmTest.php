<?php

declare(strict_types=1);

use Honed\Action\Concerns\HasConfirm;
use Honed\Action\Confirm;
use Honed\Action\Tests\Stubs\Product;

class HasConfirmTest
{
    use HasConfirm;
}

beforeEach(function () {
    $this->test = new HasConfirmTest;
});

it('sets with instance', function () {
    expect($this->test->confirm(Confirm::make('title', 'description')->constructive()))
        ->toBe($this->test)
        ->hasConfirm()->toBeTrue()
        ->getConfirm()->scoped(fn ($confirm) => $confirm
        ->getTitle()->toBe('title')
        ->getDescription()->toBe('description')
        ->getIntent()->toBe(Confirm::Constructive)
        );
});

it('sets with self-call', function () {
    expect($this->test->confirm(fn (Confirm $confirm) => $confirm
        ->title('title')
        ->description('description')
        ->submit('Accept'))
    )->toBe($this->test)
        ->hasConfirm()->toBeTrue()
        ->getConfirm()->scoped(fn ($confirm) => $confirm
            ->getTitle()->toBe('title')
            ->getDescription()->toBe('description')
            ->getSubmit()->toBe('Accept')
        );
});

it('sets with strings', function () {
    expect($this->test->confirm('title', 'description'))
        ->toBe($this->test)
        ->hasConfirm()->toBeTrue()
        ->getConfirm()->scoped(fn ($confirm) => $confirm
        ->getTitle()->toBe('title')
        ->getDescription()->toBe('description')
        );
});

it('resolves', function () {
    $product = product();

    expect($this->test->confirm(fn (Confirm $confirm) => $confirm
        ->title(fn (Product $product) => \sprintf('Delete %s', $product->name))
        ->description(fn (Product $product) => \sprintf('Are you sure you want to delete %s?', $product->name))
        ->submit('Delete')
        ->destructive()
    ))->toBe($this->test)
        ->hasConfirm()->toBeTrue()
        ->getConfirm($product)->scoped(fn ($confirm) => $confirm
            ->getTitle()->toBe(\sprintf('Delete %s', $product->name))
            ->getDescription()->toBe(\sprintf('Are you sure you want to delete %s?', $product->name))
            ->getSubmit()->toBe('Delete')
            ->getIntent()->toBe(Confirm::Destructive)
        );
});
