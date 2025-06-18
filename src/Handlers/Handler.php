<?php

declare(strict_types=1);

namespace Honed\Action\Handlers;

use Honed\Action\Exceptions\OperationNotFoundException;
use Honed\Action\Http\Data\BulkData;
use Honed\Action\Http\Data\InlineData;
use Honed\Action\Operations\Operation;
use Honed\Core\Parameters;
use Illuminate\Contracts\Database\Eloquent\Builder as BuilderContract;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\HttpFoundation\RedirectResponse;

use function array_fill_keys;

/**
 * @template TClass of mixed
 */
abstract class Handler
{
    /**
     * The instance to be used to resolve the action.
     *
     * @var TClass
     */
    protected $instance;

    /**
     * Get the key to use for selecting records.
     *
     * @return string
     */
    abstract protected function getKey();

    /**
     * Get the actions to be used to resolve the action.
     *
     * @return array<int,Operation>
     */
    abstract protected function getOperations();

    /**
     * Get the query builder to be used to retrieve resources.
     *
     * @return Builder<Model>
     */
    abstract protected function getBuilder();

    /**
     * Handle the incoming action request.
     *
     * @param  TClass  $instance
     * @param  Http\Requests\InvokableRequest  $request
     * @return Responsable|RedirectResponse
     */
    public static function handle($instance, $request)
    {
        return (new self($instance))->resolve($request);
    }

    /**
     * Handle the incoming action request using the actions from the source, and the resource provided.
     *
     * @param  Honed\Action\Http\Requests\InvokableRequest  $request
     * @return Responsable|RedirectResponse
     */
    protected function resolve($request)
    {
        $type = $request->type();

        $data = $request->toData();

        if (! $data) {
            abort(400, 'Invalid action type.');
        }

        [$action, $query] = match ($type) {
            Operation::INLINE => $this->resolveInlineOperation($data),
            Operation::BULK => $this->resolveBulkOperation($data),
            Operation::PAGE => $this->resolvePageOperation($data),
            default => abort(400, 'Invalid action type.'),
        };

        if ($this->invalidOperation($action, $query)) {
            OperationNotFoundException::throw($data->name);
        }

        $response = $this->instance->evaluate([$action, 'execute'], [$query]);

        if ($this->isResponsable($response)) {
            return $response;
        }

        return Redirect::back();
    }

    /**
     * Resolve the inline action.
     *
     * @param  InlineData  $data
     * @return array{Operation|null, TModel|null}
     */
    protected function resolveInlineOperation($data)
    {
        $builder = $this->getBuilder();

        $key = $this->getKey($builder);

        $model = $builder->where($key, $data->record)
            ->first();

        $action = Arr::first(
            $this->getOperations(),
            static fn (Operation $action) => $action->isInline()
                && $action->getName() === $data->name
        );

        return [$action, $model];
    }

    /**
     * Resolve the bulk action.
     *
     * @param  BulkData  $data
     * @return array{Operation|null, TBuilder}
     */
    protected function resolveBulkOperation($data)
    {
        $builder = $this->getBuilder();

        $key = $this->getKey($builder);

        /** @var Builder<Model> $builder */
        $builder = $data->all
            ? $builder->whereNotIn($key, $data->except)
            : $builder->whereIn($key, $data->only);

        $action = Arr::first(
            $this->getOperations(),
            static fn (Operation $action) => $action->isBulk()
                && $action->getName() === $data->name
        );

        return [$action, $builder];
    }

    /**
     * Resolve the page action.
     *
     * @param  OperationData  $data
     * @return array{Operation|null, TBuilder}
     */
    protected function resolvePageOperation($data)
    {
        $builder = $this->getBuilder();

        $action = Arr::first(
            $this->getOperations(),
            static fn (Operation $action) => $action->isPage()
                && $action->getName() === $data->name
        );

        return [$action, $builder];
    }

    /**
     * Determine if the action and query are not allowed.
     *
     * @param  Operation|null  $action
     * @param  TModel|TBuilder|null  $query
     * @return bool
     */
    protected function invalidOperation($action, $query)
    {
        if (! $action || ! $query) {
            return true;
        }

        $isBuilder = $query instanceof Builder;

        return ! $action->isAllowed(
            $this->getNamedParameters($query, $isBuilder),
            $this->getTypedParameters($query, $isBuilder)
        );
    }

    /**
     * Get the named parameters for the action.
     *
     * @param  TModel|TBuilder  $resource
     * @param  bool  $builder
     * @return array
     */
    protected function getNamedParameters($resource, $builder)
    {
        $keys = $builder
            ? ['builder', 'query', 'q']
            : ['model', 'record', 'row'];

        return array_fill_keys(
            $keys,
            $resource
        );
    }

    /**
     * Get the typed parameters for the action.
     *
     * @param  TModel|TBuilder  $resource
     * @param  bool  $builder
     * @return array
     */
    protected function getTypedParameters($resource, $builder)
    {
        $keys = $builder
            ? [Builder::class, BuilderContract::class]
            : [Model::class];

        return array_fill_keys(
            $keys,
            $resource
        );
    }

    /**
     * Determine if the result is a responsable or redirect response.
     *
     * @param  mixed  $result
     * @return bool
     */
    protected function isResponsable($result)
    {
        return $result instanceof Responsable ||
            $result instanceof RedirectResponse ||
            $result instanceof \Inertia\ResponseFactory;
    }
}
