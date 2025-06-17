<?php

declare(strict_types=1);

namespace Honed\Action\Testing;

use Honed\Action\Action;
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
            'type' => Action::PAGE,
        ], parent::getData());
    }
}
