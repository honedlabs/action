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
     */
    public static function make(): static
    {
        return resolve(static::class);
    }

    /**
     * Get as an inline action
     */
    public static function inline(): InlineOperation
    {
        return static::create(InlineOperation::class);
    }

    /**
     * Get as a bulk action
     */
    public static function bulk(): BulkOperation
    {
        return static::create(BulkOperation::class);
    }

    /**
     * Get as a page action
     */
    public static function page(): PageOperation
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
    protected static function create(string $type): Operation
    {
        return static::make()->definition(new $type());
    }
}
