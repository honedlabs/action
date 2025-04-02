<?php

declare(strict_types=1);

namespace Honed\Action\Testing;

class RequestFactory
{
    /**
     * The URI of the fake request to post to.
     *
     * @var string
     */
    protected $uri = '/';

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
    public function inline()
    {
        return (new InlineActionRequest)->uri($this->uri);
    }

    /**
     * Create a new bulk action request instance.
     *
     * @return \Honed\Action\Testing\BulkActionRequest
     */
    public function bulk()
    {
        return (new BulkActionRequest)->uri($this->uri);
    }

    /**
     * Create a new page action request instance.
     *
     * @return \Honed\Action\Testing\PageActionRequest
     */
    public function page()
    {
        return (new PageActionRequest)->uri($this->uri);
    }
}
