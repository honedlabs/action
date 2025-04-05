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
     * @return \Honed\Action\Testing\InlineActionRequest
     */
    public static function inline()
    {
        return new InlineActionRequest;
    }

    /**
     * Create a new bulk action request instance.
     *
     * @return \Honed\Action\Testing\BulkActionRequest
     */
    public static function bulk()
    {
        return new BulkActionRequest;
    }

    /**
     * Create a new page action request instance.
     *
     * @return \Honed\Action\Testing\PageActionRequest
     */
    public static function page()
    {
        return new PageActionRequest;
    }
}
