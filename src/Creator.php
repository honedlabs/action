<?php

declare(strict_types=1);

namespace Honed\Action;

use Honed\Action\Exceptions\InvalidActionTypeException;

class Creator
{
    const Bulk = 'bulk';

    const Inline = 'inline';

    const Page = 'page';

    /**
     * Create a new action.
     */
    public function new(string $type, string $name, string|\Closure|null $label = null): Action
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
     */
    public function bulk(string $name, string|\Closure|null $label = null): BulkAction
    {
        return BulkAction::make($name, $label);
    }

    /**
     * Create a new inline action.
     */
    public function inline(string $name, string|\Closure|null $label = null): InlineAction
    {
        return InlineAction::make($name, $label);
    }

    /**
     * Create a new page action.
     */
    public function page(string $name, string|\Closure|null $label = null): PageAction
    {
        return PageAction::make($name, $label);
    }

    /**
     * Throw an invalid argument exception.
     */
    protected static function throwInvalidArgumentException(string $type): never
    {
        throw new \InvalidArgumentException(\sprintf(
            'Action type [%s] is invalid.', $type
        ));
    }
}
