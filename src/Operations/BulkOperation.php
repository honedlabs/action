<?php

declare(strict_types=1);

namespace Honed\Action\Operations;

use Honed\Action\Concerns\HandlesBulkActions;

class BulkOperation extends Operation
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

        $this->type(self::BULK);
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
    public function toArray()
    {
        return [
            ...parent::toArray(),
            'keepSelected' => $this->keepsSelected(),
        ];
    }

    /**
     * Define the bulk operation instance.
     *
     * @param  $this  $operation
     * @return $this
     */
    protected function definition(self $operation): self
    {
        return $operation;
    }
}
