<?php

declare(strict_types=1);

use Honed\Action\ActionGroup;
use Honed\Action\Concerns\HasEncoder;
use Honed\Action\Tests\Stubs\Product;
use Honed\Action\Tests\Stubs\ProductActions;

beforeEach(function () {
    $this->test = new class()
    {
        use HasEncoder;

        public static function baseClass()
        {
            return ActionGroup::class;
        }
    };

    // Null the encoder and decoder as they are static
    $this->test->encoder();
    $this->test->decoder();
});

it('encodes using encrypter', function () {
    $encoded = $this->test->encode('test');

    expect($encoded)->toBeString();

    $decoded = $this->test->decode($encoded);

    expect($decoded)->toBe('test');
});

it('encodes using custom encoder', function () {
    $this->test->encoder(fn ($value) => base64_encode($value));
    $this->test->decoder(fn ($value) => base64_decode($value));

    $encoded = $this->test->encode('test');

    expect($encoded)->toBeString();

    $decoded = $this->test->decode($encoded);

    expect($decoded)->toBe('test');
});

it('retrieves primitive', function () {
    $actions = ProductActions::make();

    expect($actions)
        ->tryFrom($actions->getRouteKey())
        ->toBeInstanceOf(ProductActions::class);

    expect($actions)
        ->tryFrom(ActionGroup::make()->getRouteKey())
        ->toBeNull();
});

it('must have a make method', function () {
    expect($this->test->tryFrom(Product::class))
        ->toBeNull();
});
