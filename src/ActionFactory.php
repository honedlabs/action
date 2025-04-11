<?php

declare(strict_types=1);

namespace Honed\Action;

class ActionFactory
{
    const INLINE = 'inline';

    const BULK = 'bulk';

    const PAGE = 'page';

    /**
     * Create a new action.
     *
     * @param  'bulk'|'inline'|'page'|string  $type
     * @param  string  $name
     * @param  string|\Closure|null  $label
     * @return \Honed\Action\Action
     *
     * @throws \InvalidArgumentException
     */
    public function new($type, $name, $label = null)
    {
        return match ($type) {
            self::BULK => $this->bulk($name, $label),
            self::INLINE => $this->inline($name, $label),
            self::PAGE => $this->page($name, $label),
            default => static::throwInvalidActionException($type),
        };
    }

    /**
     * Create a new bulk action.
     *
     * @param  string  $name
     * @param  string|\Closure|null  $label
     * @return \Honed\Action\BulkAction
     */
    public function bulk($name, $label = null)
    {
        return BulkAction::make($name, $label);
    }

    /**
     * Create a new inline action.
     *
     * @param  string  $name
     * @param  string|\Closure|null  $label
     * @return \Honed\Action\InlineAction
     */
    public function inline($name, $label = null)
    {
        return InlineAction::make($name, $label);
    }

    /**
     * Create a new page action.
     *
     * @param  string  $name
     * @param  string|\Closure|null  $label
     * @return \Honed\Action\PageAction
     */
    public function page($name, $label = null)
    {
        return PageAction::make($name, $label);
    }

    /**
     * Create a new action group.
     *
     * @param  \Honed\Action\Action|iterable<int, \Honed\Action\Action>  ...$actions
     * @return \Honed\Action\ActionGroup<\Illuminate\Database\Eloquent\Model, \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>>
     */
    public function group(...$actions)
    {
        return ActionGroup::make(...$actions);
    }

    /**
     * Throw an invalid argument exception.
     *
     * @param  string  $type
     * @return never
     *
     * @throws \InvalidArgumentException
     */
    public static function throwInvalidActionException($type)
    {
        throw new \InvalidArgumentException(
            \sprintf(
                'Action type [%s] is invalid.',
                $type
            )
        );
    }
}
