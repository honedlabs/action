<?php

declare(strict_types=1);

namespace Honed\Action\Testing;

use Honed\Action\ActionFactory;

class PageRequest extends FakeRequest
{
    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return \array_merge([
            'type' => ActionFactory::PAGE,
        ], parent::getData());
    }
}
