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
     * Define the parameters of the operation.
     *
     * @template TOperation of Operation
     *
     * @param  TOperation  $operation
     * @return TOperation
     */
    abstract protected function definition(Operation $operation): Operation;

    /**
     * Create a new instance of the assembler.
     *
     * @return static
     */
    public static function make()
    {
        return resolve(static::class);
    }

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
     * @template TOperation of Operation
     *
     * @param  class-string<TOperation>  $type
     * @return TOperation
     */
    protected static function create($type)
    {
        return static::make()->definition(new $type());
    }
}
