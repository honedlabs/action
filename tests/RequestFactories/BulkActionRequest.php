<?php

declare(strict_types=1);

namespace Honed\Action\Tests\RequestFactories;

use Honed\Action\Creator;
use Worksome\RequestFactories\RequestFactory;

class BulkActionRequest extends RequestFactory
{
    public function definition(): array
    {
        return [
            'name' => 'update',
            'type' => Creator::Bulk,
            'all' => false,
            'except' => [],
            'only' => [],
        ];
    }
}
