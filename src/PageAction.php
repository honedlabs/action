<?php

declare(strict_types=1);

namespace Honed\Action;

class PageAction extends Action
{
    use Concerns\HasBulkActions;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->type(ActionFactory::Page);
    }
}
