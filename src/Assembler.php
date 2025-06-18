<?php

declare(strict_types=1);

namespace Honed\Action;

use Honed\Action\Operations\BulkOperation;
use Honed\Action\Operations\InlineOperation;
use Honed\Action\Operations\Operation;
use Honed\Action\Operations\PageOperation;

abstract class Assembler
{
    /**
     * Configure the action.
     */
    abstract protected function definition(Operation $operation): Operation;

    /**
     * Get as an inline action
     *
     * @return InlineOperation
     */
    public static function inline()
    {
        return static::create(InlineOperation::class);
    }

    /**
     * Get as a bulk action
     *
     * @return BulkOperation
     */
    public static function bulk()
    {
        return static::create(BulkOperation::class);
    }

    /**
     * Get as a page action
     *
     * @return PageOperation
     */
    public static function page()
    {
        return static::create(PageOperation::class);
    }

    /**
     * The type of the action to be generated.
     *
     * @param  class-string<Operation>  $type
     * @return Operation
     */
    public static function create($type)
    {
        return resolve(static::class)->definition(new $type());
    }
}
