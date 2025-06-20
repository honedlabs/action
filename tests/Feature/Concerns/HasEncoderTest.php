<?php

declare(strict_types=1);

use Honed\Action\Batch;

afterEach(function () {
    Batch::flushState();
});

it('encodes using encrypter', function () {
    $encoded = Batch::encode('test');

    expect($encoded)->toBeString();

    $decoded = Batch::decode($encoded);

    expect($decoded)->toBe('test');
});

it('encodes using custom encoder', function () {
    Batch::encoder(fn ($value) => base64_encode($value));
    Batch::decoder(fn ($value) => base64_decode($value));

    $encoded = Batch::encode('test');

    expect($encoded)->toBeString();

    $decoded = Batch::decode($encoded);

    expect($decoded)->toBe('test');
});
