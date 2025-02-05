<?php

declare(strict_types=1);

namespace Honed\Action\Tests\RequestFactories;

use Honed\Action\Creator;
use Worksome\RequestFactories\RequestFactory;

class InlineActionRequest extends RequestFactory
{
    public function definition(): array
    {
        return [
            'name' => 'update',
            'type' => Creator::Inline,
            'id' => 1,
        ];
    }
}
