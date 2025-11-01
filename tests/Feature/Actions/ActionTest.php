<?php

declare(strict_types=1);

use Honed\Action\Actions\UpdateAction;

it('resolves action', function () {
    expect(UpdateAction::make())
        ->toBeInstanceOf(UpdateAction::class);
});
