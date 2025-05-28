<?php

declare(strict_types=1);

use Honed\Action\Exceptions\InvalidActionException;

it('constructs', function () {
    $exception = new InvalidActionException('test');

    expect($exception->getStatusCode())->toBe(400);
});

it('throws', function () {
    InvalidActionException::throw('test');
})->throws(InvalidActionException::class);
