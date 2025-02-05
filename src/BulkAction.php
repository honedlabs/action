<?php

declare(strict_types=1);

namespace Honed\Action;

use Illuminate\Support\Traits\ForwardsCalls;

class BulkAction extends Action
{
    use Concerns\HasBulkActions;
    use Concerns\HasConfirm;
    use Concerns\KeepsSelected;

    protected $type = Creator::Bulk;

    public function toArray(): array
    {
        return \array_merge(parent::toArray(), [
            'action' => $this->hasAction(),
            'keepSelected' => $this->keepsSelected(),
            'confirm' => $this->getConfirm(),
        ]);
    }
}
