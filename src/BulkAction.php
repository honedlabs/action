<?php

declare(strict_types=1);

namespace Honed\Action;

use Honed\Action\Concerns\HandlesBulkActions;

use function array_merge;

class BulkAction extends Action
{
    use HandlesBulkActions;
    
    /**
     * Whether the action keeps the records selected after successful execution.
     *
     * @var bool
     */
    protected $keepSelected = false;

    /**
     * Provide the instance with any necessary setup.
     *
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();

        $this->type(Action::BULK);
    }

    /**
     * Set the action to keep the records selected.
     *
     * @param  bool  $keep
     * @return $this
     */
    public function keepSelected($keep = true)
    {
        $this->keepSelected = $keep;

        return $this;
    }

    /**
     * Determine if the action keeps the records selected.
     *
     * @return bool
     */
    public function keepsSelected()
    {
        return $this->keepSelected;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray($named = [], $typed = [])
    {
        return array_merge(parent::toArray($named, $typed), [
            'keepSelected' => $this->keepsSelected(),
        ]);
    }
}
