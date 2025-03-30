<?php

declare(strict_types=1);

namespace Honed\Action;

class ActionFactory
{
    const Inline = 'inline';

    const Bulk = 'bulk';

    const Page = 'page';

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
            self::Bulk => $this->bulk($name, $label),
            self::Inline => $this->inline($name, $label),
            self::Page => $this->page($name, $label),
            default => static::throwInvalidArgumentException($type),
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
     * @return \Honed\Action\ActionGroup
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
    protected static function throwInvalidArgumentException($type)
    {
        throw new \InvalidArgumentException(\sprintf(
            'Action type [%s] is invalid.', $type
        ));
    }
}
