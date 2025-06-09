<?php

declare(strict_types=1);

namespace Honed\Action;

use Honed\Action\Concerns\HandlesBulkActions;

use function array_merge;

class BulkAction extends Action
{
    use HandlesBulkActions;

    /**
     * {@inheritdoc}
     */
    protected $type = 'bulk';

    /**
     * Whether the action keeps the records selected after successful execution.
     *
     * @var bool
     */
    protected $keepSelected = false;

    /**
     * Flush the global configuration state.
     *
     * @return void
     */
    public static function flushState()
    {
        static::$shouldChunk = false;
        static::$shouldChunkById = true;
        static::$useChunkSize = 500;
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
