<?php

declare(strict_types=1);

use Honed\Action\Exceptions\OperationNotFoundException;

it('constructs', function () {
    $exception = new OperationNotFoundException('create');

    expect($exception->getStatusCode())->toBe(404);
});

it('throws', function () {
    OperationNotFoundException::throw('create');
})->throws(OperationNotFoundException::class);
