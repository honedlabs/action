<?php

declare(strict_types=1);

namespace Honed\Action;

use Honed\Core\Concerns\HasDestination;
use Illuminate\Support\Traits\ForwardsCalls;

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
