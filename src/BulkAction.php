<?php

declare(strict_types=1);

namespace Honed\Action;

class BulkAction extends Action
{
    use Concerns\HasBulkActions;
    use Concerns\KeepsSelected;

    /**
     * {@inheritdoc}
     */
    public function setUp(): void
    {
        $this->type(Creator::Bulk);
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return \array_merge(parent::toArray(), [
            'keepSelected' => $this->keepsSelected(),
        ]);
    }
}
