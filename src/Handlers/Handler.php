<?php

declare(strict_types=1);

namespace Honed\Action\Handlers;

use Honed\Action\Exceptions\InvalidOperationException;
use Honed\Action\Exceptions\OperationForbiddenException;
use Honed\Action\Exceptions\OperationNotFoundException;
use Honed\Action\Http\Data\BulkData;
use Honed\Action\Http\Data\InlineData;
use Honed\Action\Http\Data\PageData;
use Honed\Action\Operations\Operation;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\HttpFoundation\RedirectResponse;

use function array_fill_keys;

/**
 * @template TClass of \Honed\Core\Primitive
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
     * Get the operations to be used to resolve the action.
     *
     * @return array<int,Operation>
     */
    abstract protected function getOperations();

    /**
     * Handle the incoming action request.
     *
     * @param  TClass  $instance
     * @param  \Honed\Action\Http\Requests\InvokableRequest  $request
     * @return Responsable|RedirectResponse
     */
    public function handle($instance, $request)
    {
        return $this->instance($instance)->resolve($request);
    }

    /**
     * Set the instance to be used to resolve the action.
     *
     * @param  TClass  $instance
     * @return $this
     */
    public function instance($instance)
    {
        $this->instance = $instance;

        return $this;
    }

    /**
     * Get the instance to be used to resolve the action.
     *
     * @return TClass
     */
    public function getInstance()
    {
        return $this->instance;
    }

    /**
     * Handle the incoming request using the operations from the source, and the resource provided.
     *
     * @param  \Honed\Action\Http\Requests\InvokableRequest  $request
     * @return Responsable|RedirectResponse
     *
     * @throws InvalidOperationException
     * @throws OperationNotFoundException
     * @throws OperationForbiddenException
     */
    protected function resolve($request)
    {
        $data = $request->toData();

        if (! $data) {
            InvalidOperationException::throw();
        }

        [$operation, $model] = $this->prepare($data);

        if (! $operation) {
            OperationNotFoundException::throw($data->name);
        }

        $named = $this->getNamedParameters($model);
        $typed = $this->getTypedParameters($model);

        if (! $operation->isAllowed($named, $typed)) {
            // @phpstan-ignore-next-line argument.type
            OperationForbiddenException::throw($operation->getName());
        }

        $response = $this->instance->evaluate(
            $operation->callback(), $named, $typed
        );

        return $this->isResponsable($response)
            ? $response
            : Redirect::back();
    }

    /**
     * Prepare the data and instance to handle the operation.
     *
     * @param  PageData  $data
     * @return array{Operation|null, Model|null}
     */
    protected function prepare($data)
    {
        return match (true) {
            $data instanceof InlineData => $this->prepareForInlineOperation($data),
            $data instanceof BulkData => $this->prepareForBulkOperation($data),
            default => $this->prepareForPageOperation($data),
        };
    }

    /**
     * Prepare the data and instance to handle the inline operation.
     *
     * @param  InlineData  $data
     * @return array{Operation|null, Model|null}
     */
    protected function prepareForInlineOperation($data)
    {
        /** @var Model|null $model */
        $model = $this->getRecord($data->record);

        if (! $model) {
            abort(404);
        }

        $action = Arr::first(
            $this->getOperations(),
            fn (Operation $action) => $action->isInline()
                && $action->getName() === $data->name
        );

        return [$action, $model];
    }

    /**
     * Prepare the data and instance to handle the bulk operation.
     *
     * @param  BulkData  $data
     * @return array{Operation|null, null}
     */
    protected function prepareForBulkOperation($data)
    {
        $data->all
            ? $this->getException($data->except)
            : $this->getOnly($data->only);

        $action = Arr::first(
            $this->getOperations(),
            static fn (Operation $action) => $action->isBulk()
                && $action->getName() === $data->name
        );

        return [$action, null];
    }

    /**
     * Prepare the data and instance to handle the page operation.
     *
     * @param  PageData  $data
     * @return array{Operation|null, null}
     */
    protected function prepareForPageOperation($data)
    {
        $action = Arr::first(
            $this->getOperations(),
            static fn (Operation $action) => $action->isPage()
                && $action->getName() === $data->name
        );

        return [$action, null];
    }

    /**
     * Get the named parameters for the action.
     *
     * @template TResource of array<string,mixed>|\Illuminate\Database\Eloquent\Model
     *
     * @param  TResource|null  $resource
     * @return array<string,TResource>
     */
    protected function getNamedParameters($resource)
    {
        if (! $resource) {
            return [];
        }

        return array_fill_keys(
            ['model', 'record', 'row'],
            $resource
        );
    }

    /**
     * Get the typed parameters for the action.
     *
     * @template TResource of array<string,mixed>|\Illuminate\Database\Eloquent\Model
     *
     * @param  TResource|null  $resource
     * @return array<class-string,TResource>
     */
    protected function getTypedParameters($resource)
    {
        if (! $resource || ! $resource instanceof Model) {
            return [];
        }

        return array_fill_keys(
            [Model::class, $resource::class],
            $resource
        );
    }

    /**
     * Determine if the result is a responsable or redirect response.
     *
     * @param  mixed  $result
     * @return ($result is Responsable|RedirectResponse ? true : false)
     */
    protected function isResponsable($result)
    {
        return $result instanceof Responsable
            || $result instanceof RedirectResponse;
    }

    /**
     * Get the record for the given id.
     *
     * @param  int|string  $id
     * @return array<string, mixed>|Model|null
     */
    protected function getRecord($id)
    {
        /** @var array<string, mixed>|Model|null */
        return $this->instance->evaluate(
            fn ($builder) => $builder
                ->where($this->getKey(), $id)
                ->first()
        );
    }

    /**
     * Apply an exception clause to the record builder.
     *
     * @param  array<int, mixed>  $ids
     * @return void
     */
    protected function getException($ids)
    {
        $this->instance->evaluate(
            fn ($builder) => $builder->whereNotIn($this->getKey(), $ids)
        );
    }

    /**
     * Apply an only clause to the record builder.
     *
     * @param  array<int, mixed>  $ids
     * @return void
     */
    protected function getOnly($ids)
    {
        $this->instance->evaluate(
            fn ($builder) => $builder->whereIn($this->getKey(), $ids)
        );
    }
}
