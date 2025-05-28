<?php

declare(strict_types=1);

namespace Honed\Action\Testing;

class RequestFactory
{
    /**
     * Create a new fake action request instance.
     *
     * @return static
     */
    public static function make()
    {
        return resolve(static::class);
    }

    /**
     * Create a new inline action request instance.
     *
     * @return InlineRequest
     */
    public static function inline()
    {
        return new InlineRequest();
    }

    /**
     * Create a new bulk action request instance.
     *
     * @return BulkRequest
     */
    public static function bulk()
    {
        return new BulkRequest();
    }

    /**
     * Create a new page action request instance.
     *
     * @return PageRequest
     */
    public static function page()
    {
        return new PageRequest();
    }
}
