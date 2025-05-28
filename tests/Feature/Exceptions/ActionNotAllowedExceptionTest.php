<?php

declare(strict_types=1);

use Honed\Action\Exceptions\ActionNotAllowedException;

it('constructs', function () {
    $exception = new ActionNotAllowedException('test');

    expect($exception->getStatusCode())->toBe(403);
});

it('throws', function () {
    ActionNotAllowedException::throw('test');
})->throws(ActionNotAllowedException::class);
