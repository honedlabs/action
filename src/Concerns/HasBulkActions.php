<?php

declare(strict_types=1);

namespace Honed\Action\Concerns;

use Illuminate\Support\Collection;
use Honed\Action\Contracts\HasHandler;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait HasBulkActions
{
    use HasAction;
    use ChunksBuilder;

    /**
     * Execute the action handler using the provided data.
     *
     * @template TModel of \Illuminate\Database\Eloquent\Model
     * 
     * @param  \Illuminate\Database\Eloquent\Builder<TModel>  $builder
     * @return \Illuminate\Contracts\Support\Responsable|\Illuminate\Http\RedirectResponse|bool|void
     */
    public function execute($builder)
    {
        if (! $this->hasAction()) {
            return;
        }

        [$model, $singular, $plural] = $this->getParameterNames($builder);

        $handler = $this instanceof HasHandler;

        /**
         * @phpstan-var callable|\Closure
         */
        $callback = $handler ? [$this, 'handle'] : $this->getAction(); // @phpstan-ignore-line

        $parameters = $handler 
            ? $this->getHandleParameters() 
            : (new \ReflectionFunction($callback))->getParameters(); // @phpstan-ignore-line

        $retrieveRecords = $this->isCollectionCallback($parameters, $plural);

        $singularAccess = $this->isModelCallback($parameters, $model, $singular);

        return match (true) {
            $this->chunks() => $this->chunkRecords($builder, $callback, $singularAccess),

            $retrieveRecords && $handler => \call_user_func($callback, $builder->get()),

            $retrieveRecords => $this->evaluateCallbackWithRecords($builder, $callback, $plural), // @phpstan-ignore-line

            $singularAccess => $builder->get()->each(fn ($model) => \call_user_func($callback, $model)),

            $handler => \call_user_func($callback, $builder),

            default => $this->evaluate($callback, [
                'query' => $builder,
                'builder' => $builder,
            ], [
                Builder::class => $builder,
            ]),
        };
    }

    /**
     * Evaluate the action handler with retrieved records.
     * 
     * @template TModel of \Illuminate\Database\Eloquent\Model
     * 
     * @param  \Illuminate\Database\Eloquent\Builder<TModel>  $builder
     */
    private function evaluateCallbackWithRecords(Builder $builder, \Closure $callback, string $plural): mixed
    {
        $records = $builder->get();

        return $this->evaluate($callback, [
            'records' => $records,
            'collection' => $records,
            $plural => $records,
        ], [
            Collection::class => $records,
        ]);
    }

    /**
     * Retrieve the parameters for the handle method.
     *
     * @return array<int,\ReflectionParameter>
     */
    private function getHandleParameters(): array
    {
        $methods = (new \ReflectionClass($this))->getMethods();

        return collect($methods)
            ->first(fn (\ReflectionMethod $method) => $method->getName() === 'handle')
            ?->getParameters() ?? [];
    }

    /**
     * Determine if the action executable requires the records to be retrieved.
     *
     * @param  array<int,\ReflectionParameter>  $parameters
     */
    private function isCollectionCallback(array $parameters, string $plural): bool
    {
        return collect($parameters)
            ->some(fn (\ReflectionParameter $parameter) => ($t = $parameter->getType()) instanceof \ReflectionNamedType && \in_array($t->getName(), [Collection::class])
                    || \in_array($parameter->getName(), ['records', 'collection', $plural])
            );
    }

    /**
     * Determine if the action executable requires a model to be retrieved, and then acts on individual collection records.
     *
     * @param  array<int,\ReflectionParameter>  $parameters
     */
    private function isModelCallback(array $parameters, Model $model, string $singular): bool
    {
        return collect($parameters)
            ->some(fn (\ReflectionParameter $parameter) => ($t = $parameter->getType()) instanceof \ReflectionNamedType && \in_array($t->getName(), [Model::class, $model::class])
                    || \in_array($parameter->getName(), ['model', 'record', $singular])
            );
    }

}
