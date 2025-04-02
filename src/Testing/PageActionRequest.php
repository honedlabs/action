<?php

declare(strict_types=1);

namespace Honed\Action\Testing;

use Honed\Action\ActionFactory;

class PageActionRequest extends FakeActionRequest
{
    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return \array_merge([
            'type' => ActionFactory::Page,
        ], $this->data);
    }
}
