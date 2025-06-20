<?php

declare(strict_types=1);

use Honed\Action\Exceptions\InvalidOperationException;

it('constructs', function () {
    $exception = new InvalidOperationException();

    expect($exception->getStatusCode())->toBe(400);
});

it('throws', function () {
    InvalidOperationException::throw();
})->throws(InvalidOperationException::class);
