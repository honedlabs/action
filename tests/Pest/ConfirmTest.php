<?php

declare(strict_types=1);

use Honed\Action\Confirm;
use Honed\Action\Tests\Stubs\Product;

beforeEach(function () {
    $this->test = Confirm::make();
});

it('has array representation', function () {
    expect($this->test->toArray())
        ->toBeArray()
        ->toEqual([
            'label' => null,
            'description' => null,
            'dismiss' => 'Cancel',
            'submit' => 'Confirm',
            'intent' => null,
        ]);
});

it('resolves', function () {
    $product = product();

    expect(Confirm::make(
        fn (Product $p) => $p->name,
        fn (Product $p) => \sprintf('Are you sure you want to delete %s?', $p->name)
    )->resolve(...params($product)))
        ->toBeInstanceOf(Confirm::class)
        ->getLabel()->toBe($product->name)
        ->getDescription()->toBe(\sprintf('Are you sure you want to delete %s?', $product->name));
});

it('has dismiss', function () {
    expect($this->test)
        ->getDismiss()->toBe(config('action.confirm.dismiss'))
        ->dismiss('Back')->toBeInstanceOf(Confirm::class)
        ->getDismiss()->toBe('Back');
});

it('has submit', function () {
    expect($this->test)
        ->getSubmit()->toBe(config('action.confirm.submit'))
        ->submit('Accept')->toBeInstanceOf(Confirm::class)
        ->getSubmit()->toBe('Accept');
});

it('has intent', function () {
    expect($this->test)
        ->getIntent()->toBeNull()
        ->hasIntent()->toBeFalse()
        ->intent('danger')->toBeInstanceOf(Confirm::class)
        ->getIntent()->toBe('danger')
        ->constructive()->toBeInstanceOf(Confirm::class)
        ->getIntent()->toBe(Confirm::Constructive)
        ->destructive()->toBeInstanceOf(Confirm::class)
        ->getIntent()->toBe(Confirm::Destructive)
        ->informative()->toBeInstanceOf(Confirm::class)
        ->getIntent()->toBe(Confirm::Informative)
        ->hasIntent()->toBeTrue();
});
