<?php

declare(strict_types=1);

use Honed\Action\Exceptions\OperationForbiddenException;

it('constructs', function () {
    $exception = new OperationForbiddenException('create');

    expect($exception->getStatusCode())->toBe(403);
});

it('throws', function () {
    OperationForbiddenException::throw('create');
})->throws(OperationForbiddenException::class);
