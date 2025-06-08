<?php

declare(strict_types=1);

namespace Honed\Action;

use Honed\Action\Concerns\HandlesBulkActions;

class PageAction extends Action
{
    use HandlesBulkActions;

    /**
     * {@inheritdoc}
     */
    protected $type = 'page';

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
}
