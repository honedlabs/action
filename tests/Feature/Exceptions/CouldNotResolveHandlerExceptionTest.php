<?php

declare(strict_types=1);

use Honed\Action\Exceptions\CouldNotResolveHandlerException;

it('constructs', function () {
    $exception = new CouldNotResolveHandlerException();

    expect($exception->getStatusCode())->toBe(404);
});

it('throws', function () {
    CouldNotResolveHandlerException::throw();
})->throws(CouldNotResolveHandlerException::class);
