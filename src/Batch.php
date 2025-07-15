<?php

declare(strict_types=1);

namespace Honed\Action;

use Closure;
use Honed\Action\Operations\Operation;
use Honed\Core\Concerns\HasRecord;
use Illuminate\Container\Container;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Throwable;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model = \Illuminate\Database\Eloquent\Model
 * @template TBuilder of \Illuminate\Database\Eloquent\Builder<TModel> = \Illuminate\Database\Eloquent\Builder<TModel>
 */
class Batch extends Unit
{
    use HasRecord;

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
     * Provide the instance with any necessary setup.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->define();
    }

    /**
     * Create a new batch instance.
     *
     * @param  Operation|Batch|array<int, Operation|Batch>  $operations
     */
    public static function make(Operation|self|array $operations = []): static
    {
        return resolve(static::class)
            ->operations($operations);
    }

    /**
     * Get the parent class for the instance.
     *
     * @return class-string<self>
     */
    public static function getParentClass(): string
    {
        return self::class;
    }

    /**
     * Get a new table instance for the given model name.
     *
     * @template TClass of \Illuminate\Database\Eloquent\Model
     *
     * @param  class-string<TClass>  $modelName
     */
    public static function batchForModel(string $modelName): self
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
    public static function resolveBatchName(string $className): string
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
     */
    public static function useNamespace(string $namespace): void
    {
        static::$namespace = $namespace;
    }

    /**
     * Specify the callback that should be invoked to guess the name of a model batch.
     *
     * @param  Closure(class-string<Model>):class-string<Batch>  $callback
     */
    public static function guessBatchNamesUsing(Closure $callback): void
    {
        static::$batchNameResolver = $callback;
    }

    /**
     * Flush the global configuration state.
     */
    public static function flushState(): void
    {
        static::$batchNameResolver = null;
        static::$namespace = 'App\\Batches\\';
        parent::flushState();
    }

    /**
     * Get the application namespace for the application.
     */
    protected static function appNamespace(): string
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
        return [
            'id' => $this->getId(),
            'inline' => $this->inlineOperationsToArray($this->getRecord()),
            'bulk' => $this->bulkOperationsToArray(),
            'page' => $this->pageOperationsToArray(),
        ];
    }

    /**
     * Provide a selection of default dependencies for evaluation by name.
     *
     * @return array<int, mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByName(string $parameterName): array
    {
        return match ($parameterName) {
            'model', 'record', 'row' => [$this->getRecord()],
            default => parent::resolveDefaultClosureDependencyForEvaluationByName($parameterName),
        };
    }

    /**
     * Provide a selection of default dependencies for evaluation by type.
     *
     * @return array<int, mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByType(string $parameterType): array
    {
        $record = $this->getRecord();

        if (! $record instanceof Model) {
            return parent::resolveDefaultClosureDependencyForEvaluationByType($parameterType);
        }

        return match ($parameterType) {
            Model::class, $record::class => [$record],
            default => parent::resolveDefaultClosureDependencyForEvaluationByType($parameterType),
        };
    }
}
