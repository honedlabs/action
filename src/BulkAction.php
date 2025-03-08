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
    public function setUp()
    {
        $this->type(ActionFactory::Bulk);
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return \array_merge(parent::toArray(), [
            'keepSelected' => $this->keepsSelected(),
        ]);
    }
}
