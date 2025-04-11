<?php

declare(strict_types=1);

namespace Honed\Action\Facades;

use Honed\Action\ActionFactory;
use Illuminate\Support\Facades\Facade;

/**
 * @see \Honed\Action\ActionFactory
 *
 * @method static \Honed\Action\Action new(string $type, string $name, string|\Closure $label = null)
 * @method static \Honed\Action\BulkAction bulk(string $name, string|\Closure $label = null)
 * @method static \Honed\Action\InlineAction inline(string $name, string|\Closure $label = null)
 * @method static \Honed\Action\PageAction page(string $name, string|\Closure $label = null)
 * @method static \Honed\Action\ActionGroup<\Illuminate\Database\Eloquent\Model, \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>> group(\Honed\Action\Action|iterable<int, \Honed\Action\Action> ...$actions)
 */
class Action extends Facade
{
    /**
     * Get the root object behind the facade.
     *
     * @return \Honed\Action\ActionFactory
     */
    public static function getFacadeRoot()
    {
        // @phpstan-ignore-next-line
        return parent::getFacadeRoot();
    }

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return ActionFactory::class;
    }
}
