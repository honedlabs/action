<?php

declare(strict_types=1);

namespace Honed\Action;

use Illuminate\Support\Traits\ForwardsCalls;

class BulkAction extends Action
{
    use Concerns\HasBulkActions;
    use Concerns\KeepsSelected;

    public function setUp(): void
    {
        $this->type(Creator::Bulk);
    }

    public function toArray(): array
    {
        return \array_merge(parent::toArray(), [
            'keepSelected' => $this->keepsSelected(),
        ]);
    }
}
