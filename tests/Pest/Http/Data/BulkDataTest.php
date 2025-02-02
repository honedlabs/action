<?php

declare(strict_types=1);

use Honed\Action\Http\Data\BulkData;
use Honed\Action\Tests\RequestFactories\BulkActionRequest;

use function Pest\Laravel\post;

it('makes from request', function () {
    BulkActionRequest::new()->fake();

    $request = post('/actions');

    expect(BulkData::from(request()))
        ->toBeInstanceOf(BulkData::class)
        ->name->toBe('update')
        ->only->toBe([])
        ->except->toBe([])
        ->all->toBe(false);
});
