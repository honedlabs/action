<?php

declare(strict_types=1);

namespace Honed\Action\Testing;

use Honed\Action\ActionFactory;

class BulkActionRequest extends FakeActionRequest
{
    /**
     * Whether to include all records.
     *
     * @var bool
     */
    protected $all = false;

    /**
     * The records to include.
     *
     * @var array<string,int|string>
     */
    protected $only = [];

    /**
     * The records to exclude.
     *
     * @var array<string,int|string>
     */
    protected $except = [];

    /**
     * Set whether to include all records.
     *
     * @param  bool  $all
     * @return $this
     */
    public function all($all = true)
    {
        $this->all = $all;

        return $this;
    }

    /**
     * Determine if all records should be included.
     *
     * @return bool
     */
    public function isAll()
    {
        return $this->all;
    }

    /**
     * Set the records to include.
     *
     * @param  array<string,int|string>  $only
     * @return $this
     */
    public function only($only)
    {
        $this->only = $only;

        return $this;
    }

    /**
     * Get the records to include.
     *
     * @return array<string,int|string>
     */
    public function getOnly()
    {
        return $this->only;
    }

    /**
     * Set the records to exclude.
     *
     * @param  array<string,int|string>  $except
     * @return $this
     */
    public function except($except)
    {
        $this->except = $except;

        return $this;
    }

    /**
     * Get the records to exclude.
     *
     * @return array<string,int|string>
     */
    public function getExcept()
    {
        return $this->except;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return \array_merge([
            'type' => ActionFactory::Bulk,
            'only' => $this->getOnly(),
            'except' => $this->getExcept(),
            'all' => $this->isAll(),
        ], parent::getData());
    }
}
