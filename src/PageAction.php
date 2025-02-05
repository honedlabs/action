<?php

declare(strict_types=1);

namespace Honed\Action;

class PageAction extends Action
{
    use Concerns\HasBulkActions;

    protected $type = Creator::Page;

    public function toArray(): array
    {
        return \array_merge(parent::toArray(), [
            'action' => $this->hasAction(),
        ]);
    }
}
