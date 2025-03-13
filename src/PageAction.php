<?php

declare(strict_types=1);

namespace Honed\Action;

use Honed\Action\Concerns\HasBulkActions;

class PageAction extends Action
{
    use HasBulkActions;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->type(ActionFactory::Page);
    }
}
