<?php

declare(strict_types=1);

use Honed\Action\Exceptions\ActionNotFoundException;

it('constructs', function () {
    $exception = new ActionNotFoundException('test');

    expect($exception->getStatusCode())->toBe(404);
});

it('throws', function () {
    ActionNotFoundException::throw('test');
})->throws(ActionNotFoundException::class);
