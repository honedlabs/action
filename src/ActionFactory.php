<?php

declare(strict_types=1);

namespace Honed\Action;

use Closure;
use Honed\Action\Support\Constants;
use InvalidArgumentException;

use function sprintf;

class ActionFactory
{
    public const INLINE = 'inline';

    public const BULK = 'bulk';

    public const PAGE = 'page';

    /**
     * Throw an invalid argument exception.
     *
     * @param  string  $type
     * @return never
     *
     * @throws InvalidArgumentException
     */
    public static function throwInvalidActionException($type)
    {
        throw new InvalidArgumentException(
            sprintf(
                'Action type [%s] is invalid.',
                $type
            )
        );
    }

    /**
     * Create a new action.
     *
     * @param  'bulk'|'inline'|'page'|string  $type
     * @param  string  $name
     * @param  string|Closure|null  $label
     * @return Action
     *
     * @throws InvalidArgumentException
     */
    public function new($type, $name, $label = null)
    {
        return match ($type) {
            Constants::BULK => $this->bulk($name, $label),
            Constants::INLINE => $this->inline($name, $label),
            Constants::PAGE => $this->page($name, $label),
            default => static::throwInvalidActionException($type),
        };
    }

    /**
     * Create a new bulk action.
     *
     * @param  string  $name
     * @param  string|Closure|null  $label
     * @return BulkAction
     */
    public function bulk($name, $label = null)
    {
        return BulkAction::make($name, $label);
    }

    /**
     * Create a new inline action.
     *
     * @param  string  $name
     * @param  string|Closure|null  $label
     * @return InlineAction
     */
    public function inline($name, $label = null)
    {
        return InlineAction::make($name, $label);
    }

    /**
     * Create a new page action.
     *
     * @param  string  $name
     * @param  string|Closure|null  $label
     * @return PageAction
     */
    public function page($name, $label = null)
    {
        return PageAction::make($name, $label);
    }

    /**
     * Create a new action group.
     *
     * @param  Action|iterable<int, Action>  ...$actions
     * @return ActionGroup<\Illuminate\Database\Eloquent\Model, \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>>
     */
    public function group(...$actions)
    {
        return ActionGroup::make(...$actions);
    }
}
