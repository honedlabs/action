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
     */
    abstract protected function definition(Operation $operation): Operation;

    /**
     * Create a new instance of the assembler.
     */
    public static function make(): static
    {
        return app(static::class);
    }

    /**
     * Get as an inline action
     */
    public static function inline(?string $namespace = null): InlineOperation
    {
        /** @var InlineOperation */
        return static::namespace(InlineOperation::class, $namespace);
    }

    /**
     * Get as a bulk action
     */
    public static function bulk(?string $namespace = null): BulkOperation
    {
        /** @var BulkOperation */
        return static::namespace(BulkOperation::class, $namespace ?? 'bulk');
    }

    /**
     * Get as a page action
     */
    public static function page(?string $namespace = null): PageOperation
    {
        /** @var PageOperation */
        return static::namespace(PageOperation::class, $namespace ?? 'page');
    }

    /**
     * The type of the action to be generated.
     *
     * @param  class-string<Operation>  $type
     */
    protected static function create(string $type): Operation
    {
        $operation = app($type);

        return static::make()->definition($operation);
    }

    /**
     * Create a new operation with a namespace.
     *
     * @param  class-string<Operation>  $type
     */
    protected static function namespace(string $type, ?string $name = null): Operation
    {
        $operation = static::create($type);

        return $operation
            ->when(
                $operation->hasName() && $name,
                static fn (Operation $operation) => $operation
                    ->name($operation->getName().'_'.$name)
            );
    }
}
