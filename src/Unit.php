<?php

declare(strict_types=1);

namespace Honed\Action;

use Honed\Action\Concerns\Actionable;
use Honed\Action\Concerns\HasEncoder;
use Honed\Action\Concerns\HasKey;
use Honed\Action\Concerns\HasOperations;
use Honed\Action\Contracts\HandlesOperations;
use Honed\Action\Handlers\Handler;
use Honed\Action\Operations\Operation;
use Honed\Core\Concerns\HasRecord;
use Honed\Core\Concerns\HasResource;
use Honed\Core\Primitive;
use Illuminate\Contracts\Database\Eloquent\Builder as BuilderContract;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as DatabaseCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model = \Illuminate\Database\Eloquent\Model
 * @template TBuilder of \Illuminate\Database\Eloquent\Builder<TModel> = \Illuminate\Database\Eloquent\Builder<TModel>
 */
class Unit extends Primitive implements HandlesOperations
{
    use Actionable;
    use HasEncoder;
    use HasKey;
    use HasOperations;
    use HasRecord;
    use HasResource;

    /**
     * Decode and retrieve a primitive class.
     */
    public static function find(string $value): ?static
    {
        try {
            $primitive = static::decode($value);

            if (static::canHandleOperations($primitive)) {
                return $primitive::make();
            }

            return null;

        } catch (Throwable $th) {
            return null;
        }
    }

    /**
     * Flush the global configuration state.
     */
    public static function flushState(): void
    {
        static::$encoder = null;
        static::$decoder = null;
    }

    /**
     * Get the handler for the instance.
     *
     * @return class-string<Handler<static>>
     */
    public function getHandler(): string
    {
        return Handler::class;
    }

    /**
     * Get the route key for the instance.
     */
    public function getRouteKeyName(): string
    {
        return 'unit';
    }

    /**
     * Get the value of the model's route key.
     *
     * @return string
     */
    public function getRouteKey()
    {
        return $this->getId();
    }

    /**
     * Retrieve the model for a bound value.
     *
     * @param  string  $value
     * @param  string|null  $field
     * @return static|null
     */
    public function resolveRouteBinding($value, $field = null)
    {
        return $this->find($value);
    }

    /**
     * Retrieve the child model for a bound value.
     *
     * @param  string  $childType
     * @param  string  $value
     * @param  string|null  $field
     * @return mixed
     */
    public function resolveChildRouteBinding($childType, $value, $field = null)
    {
        if ($childType === 'operation') {
            return Arr::first(
                $this->getOperations(),
                static fn ($operation) => $operation->getName() === $value
            );
        }

        return $this->resolveRouteBinding($value, $field);
    }

    /**
     * Handle the incoming action request.
     */
    public function handle(Operation $operation, Request $request): Responsable|Response
    {
        $handler = $this->getHandler();

        return $handler::make($this)->handle($operation, $request);
    }

    /**
     * Determine if the primitive can handle operations.
     */
    protected static function canHandleOperations(mixed $primitive): bool
    {
        return is_string($primitive)
            && class_exists($primitive)
            && is_subclass_of($primitive, static::getParentClass());
    }

    /**
     * Get the instance as an array.
     *
     * @return array<string, mixed>
     */
    protected function representation(): array
    {
        return [
            'inline' => $this->inlineOperationsToArray(),
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
        $builder = $this->getBuilder();

        return match ($parameterName) {
            'builder', 'query', 'q' => [$builder],
            'collection', 'records' => [$builder->get()],
            default => parent::resolveDefaultClosureDependencyForEvaluationByName($parameterName),
        };
    }

    /**
     * Provide a base selection of default dependencies for evaluation by type.
     *
     * @param  class-string  $parameterType
     * @return array<int, mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByType(string $parameterType): array
    {
        $builder = $this->getBuilder();

        return match ($parameterType) {
            $builder::class, Builder::class, BuilderContract::class => [$builder],
            Collection::class, DatabaseCollection::class => [$builder->get()],
            default => parent::resolveDefaultClosureDependencyForEvaluationByType($parameterType),
        };
    }
}
