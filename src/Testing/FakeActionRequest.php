<?php

declare(strict_types=1);

namespace Honed\Action\Testing;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;

class FakeActionRequest
{
    /**
     * The ID of the handler.
     *
     * @var string|null
     */
    protected $id;

    /**
     * The name of the action.
     *
     * @var string|null
     */
    protected $name;

    /**
     * The URI of the fake request to post to.
     *
     * @var string
     */
    protected $uri = '/';

    /**
     * Whether to fill in details.
     *
     * @var bool
     */
    protected $fill = false;

    /**
     * The data of the fake request.
     *
     * @var array<string,mixed>
     */
    protected $data = [];

    /**
     * Set the ID of the handler.
     *
     * @param  string  $id
     * @return $this
     */
    public function id($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the ID of the handler.
     *
     * @return string|null
     */
    public function getId()
    {
        if (! $this->id && $this->fills()) {
            return Str::uuid()->toString();
        }

        return $this->id;
    }

    /**
     * Set the name of the action.
     *
     * @param  string  $name
     * @return $this
     */
    public function name($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the name of the action.
     *
     * @return string|null
     */
    public function getName()
    {
        if (! $this->name && $this->fills()) {
            return fake()->word();
        }

        return $this->name;
    }

    /**
     * Set the URI of the fake request.
     *
     * @param  string  $uri
     * @return $this
     */
    public function uri($uri)
    {
        $this->uri = $uri;

        return $this;
    }

    /**
     * Get the URI for the fake request.
     *
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Whether to fill in details.
     *
     * @param  bool  $fill
     * @return $this
     */
    public function fill($fill = true)
    {
        $this->fill = $fill;

        return $this;
    }

    /**
     * Whether to fill in details.
     *
     * @return bool
     */
    public function fills()
    {
        return $this->fill;
    }

    /**
     * Set the data of the fake request.
     *
     * @param  array<string,mixed>  $data
     * @return $this
     */
    public function data($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get the data of the fake request.
     *
     * @return array<string,mixed>
     */
    public function getData()
    {
        return \array_merge([
            'id' => $this->getId(),
            'name' => $this->getName(),
        ], $this->data);
    }

    /**
     * Create the fake request.
     *
     * @return \Illuminate\Http\Request
     */
    public function create()
    {
        return Request::create(
            $this->getUri(),
            HttpFoundationRequest::METHOD_POST,
            $this->getData()
        );
    }
}
