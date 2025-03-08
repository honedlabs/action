<?php

declare(strict_types=1);

namespace Honed\Action\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Honed\Action\ActionFactory
 *
 * @method static \Honed\Action\Action new(string $type, string $name, string|\Closure $label = null)
 * @method static \Honed\Action\BulkAction bulk(string $name, string|\Closure $label = null)
 * @method static \Honed\Action\InlineAction inline(string $name, string|\Closure $label = null)
 * @method static \Honed\Action\PageAction page(string $name, string|\Closure $label = null)
 */
class Action extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Honed\Action\ActionFactory::class;
    }
}
