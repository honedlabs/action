<?php

declare(strict_types=1);

namespace Honed\Action\Testing;

use Honed\Action\Support\Constants;

use function array_merge;

class PageRequest extends FakeRequest
{
    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return array_merge([
            'type' => Constants::PAGE,
        ], parent::getData());
    }
}
