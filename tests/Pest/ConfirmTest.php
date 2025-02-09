<?php

declare(strict_types=1);

use Honed\Action\Confirm;
use Honed\Action\Tests\Stubs\Product;

beforeEach(function () {
    $this->test = Confirm::make();
});

it('makes', function () {
    expect($this->test)->toBeInstanceOf(Confirm::class);
});

it('has array representation', function () {
    expect($this->test->toArray())
        ->toBeArray()
        ->toHaveKeys(['name', 'description', 'dismiss', 'submit', 'intent'])
        ->toEqual([
            'name' => null,
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
        ->getName()->toBe($product->name)
        ->getDescription()->toBe(\sprintf('Are you sure you want to delete %s?', $product->name));
});

it('has dismiss', function () {
    expect($this->test)
        ->getDismiss()->toBe('Cancel')
        ->dismiss('Back')->toBeInstanceOf(Confirm::class)
        ->getDismiss()->toBe('Back');
});

it('has submit', function () {
    expect($this->test)
        ->getSubmit()->toBe('Confirm')
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
