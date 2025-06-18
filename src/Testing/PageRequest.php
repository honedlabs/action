<?php

declare(strict_types=1);

namespace Honed\Action\Testing;

use Honed\Action\Operations\Operation;

class PageRequest extends FakeRequest
{
    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return [
            'type' => Operation::PAGE,
            ...parent::getData(),
        ];
    }
}
