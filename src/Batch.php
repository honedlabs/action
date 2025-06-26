<?php

declare(strict_types=1);

namespace Honed\Action;

use Closure;
use Honed\Action\Concerns\CanHandleOperations;
use Honed\Action\Concerns\HasKey;
use Honed\Action\Contracts\Handler;
use Honed\Action\Contracts\HandlesOperations;
use Honed\Action\Handlers\BatchHandler;
use Honed\Action\Operations\Operation;
use Honed\Core\Concerns\HasRecord;
use Honed\Core\Concerns\HasResource;
use Honed\Core\Primitive;
use Illuminate\Container\Container;
use Illuminate\Contracts\Database\Eloquent\Builder as BuilderContract;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as DatabaseCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Throwable;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model = \Illuminate\Database\Eloquent\Model
 * @template TBuilder of \Illuminate\Database\Eloquent\Builder<TModel> = \Illuminate\Database\Eloquent\Builder<TModel>
 */
class Batch extends Primitive implements HandlesOperations
{
    use CanHandleOperations;
    use HasKey;
    use HasRecord;
    use HasResource;

    /**
     * The default namespace where batches reside.
     *
     * @var string
     */
    public static $namespace = 'App\\Batches\\';

    /**
     * The identifier to use for evaluation.
     *
     * @var string
     */
    protected $evaluationIdentifier = 'batch';

    /**
     * How to resolve the batch for the given model name.
     *
     * @var (Closure(class-string<Model>):class-string<Batch>)|null
     */
    protected static $batchNameResolver = null;

    /**
     * Create a new batch instance.
     *
     * @param  Operation|Batch|array<int, Operation|Batch>  $operations
     * @return static
     */
    public static function make($operations = [])
    {
        return resolve(static::class)
            ->operations($operations);
    }

    /**
     * Get a new table instance for the given model name.
     *
     * @template TClass of \Illuminate\Database\Eloquent\Model
     *
     * @param  class-string<TClass>  $modelName
     * @return self
     */
    public static function batchForModel($modelName)
    {
        $batch = static::resolveBatchName($modelName);

        return $batch::make();
    }

    /**
     * Get the table name for the given model name.
     *
     * @param  class-string<Model>  $className
     * @return class-string<Batch>
     */
    public static function resolveBatchName($className)
    {
        $resolver = static::$batchNameResolver ?? function (string $className) {
            $appNamespace = static::appNamespace();

            $className = Str::startsWith($className, $appNamespace.'Models\\')
                ? Str::after($className, $appNamespace.'Models\\')
                : Str::after($className, $appNamespace);

            /** @var class-string<Batch> */
            return static::$namespace.$className.'Batch';
        };

        return $resolver($className);
    }

    /**
     * Specify the default namespace that contains the application's model batches.
     *
     * @param  string  $namespace
     * @return void
     */
    public static function useNamespace($namespace)
    {
        static::$namespace = $namespace;
    }

    /**
     * Specify the callback that should be invoked to guess the name of a model batch.
     *
     * @param  Closure(class-string<Model>):class-string<Batch>  $callback
     * @return void
     */
    public static function guessBatchNamesUsing($callback)
    {
        static::$batchNameResolver = $callback;
    }

    /**
     * Flush the global configuration state.
     *
     * @return void
     */
    public static function flushState()
    {
        static::$encoder = null;
        static::$decoder = null;
        static::$batchNameResolver = null;
        static::$namespace = 'App\\Batches\\';
    }

    /**
     * Get the route key for the instance.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'action';
    }

    /**
     * Get the handler for the instance.
     *
     * @return class-string<Handlers\Handler<self>>
     */
    public function getHandler() // @phpstan-ignore-line
    {
        /** @var class-string<Handlers\Handler<self>> */
        return config('action.handler', BatchHandler::class);
    }

    /**
     * Get the application namespace for the application.
     *
     * @return string
     */
    protected static function appNamespace()
    {
        try {
            return Container::getInstance()
                ->make(Application::class)
                ->getNamespace();
        } catch (Throwable) {
            return 'App\\';
        }
    }

    /**
     * Get the instance as an array.
     *
     * @return array<string, mixed>
     */
    protected function representation(): array
    {
        $operations = [
            'inline' => $this->inlineOperationsToArray($this->getRecord()),
            'bulk' => $this->bulkOperationsToArray(),
            'page' => $this->pageOperationsToArray(),
        ];

        if ($this->isActionable()) {
            return [
                'id' => $this->getRouteKey(),
                'endpoint' => $this->getEndpoint(),
                ...$operations,
            ];
        }

        return $operations;
    }

    /**
     * Define the operations for the batch.
     *
     * @param  $this  $batch
     * @return $this
     */
    protected function definition(self $batch): self
    {
        return $batch;
    }

    /**
     * Provide a selection of default dependencies for evaluation by name.
     *
     * @param  string  $parameterName
     * @return array<int, mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByName($parameterName)
    {
        return match ($parameterName) {
            'model', 'record', 'row' => [$this->getRecord()],
            'builder', 'query', 'q' => [$this->getBuilder()],
            'collection', 'records' => [$this->getBuilder()->get()],
            default => parent::resolveDefaultClosureDependencyForEvaluationByName($parameterName),
        };
    }

    /**
     * Provide a selection of default dependencies for evaluation by type.
     *
     * @param  string  $parameterType
     * @return array<int, mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByType($parameterType)
    {
        $record = $this->getRecord();

        if (! $record instanceof Model) {
            return $this->resolveBatchClosureDependencyForEvaluationByType($parameterType);
        }

        return match ($parameterType) {
            Model::class, $record::class => [$record],
            default => $this->resolveBatchClosureDependencyForEvaluationByType($parameterType),
        };
    }

    /**
     * Provide a base selection of default dependencies for evaluation by type.
     *
     * @param  string  $parameterType
     * @return array<int, mixed>
     */
    protected function resolveBatchClosureDependencyForEvaluationByType($parameterType)
    {
        return match ($parameterType) {
            Builder::class, BuilderContract::class => [$this->getBuilder()],
            Collection::class, DatabaseCollection::class => [$this->getBuilder()->get()],
            default => parent::resolveDefaultClosureDependencyForEvaluationByType($parameterType),
        };
    }
}
