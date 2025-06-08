<?php

declare(strict_types=1);

namespace Honed\Action;

use Honed\Action\Exceptions\ActionNotAllowedException;
use Honed\Action\Exceptions\ActionNotFoundException;
use Honed\Action\Exceptions\InvalidActionException;
use Honed\Action\Http\Data\ActionData;
use Honed\Action\Http\Data\BulkData;
use Honed\Action\Http\Data\InlineData;
use Honed\Core\Concerns\HasResource;
use Honed\Core\Parameters;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Arr;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 * @template TBuilder of \Illuminate\Database\Eloquent\Builder<TModel>
 */
class Handler
{
    /**
     * @use \Honed\Core\Concerns\HasResource<TModel, TBuilder>
     */
    use HasResource;

    /**
     * List of the available actions.
     *
     * @var array<int,Action>
     */
    protected $actions = [];

    /**
     * The key to use for selecting records.
     *
     * @var string|null
     */
    protected $key;

    /**
     * Make a new handler instance.
     *
     * @param  TBuilder  $builder
     * @param  array<int,Action>  $actions
     * @param  string|null  $key
     * @return static
     */
    public static function make($instance, $actions = [], $key = null)
    {
        return resolve(static::class)
            ->withResource($builder)
            ->actions($actions)
            ->key($key);
    }

    /**
     * Throw an invalid argument exception.
     *
     * @param  string  $type
     * @return never
     *
     * @throws InvalidArgumentException
     */
    public static function throwInvalidActionTypeException($type)
    {
        throw new InvalidArgumentException(
            "Action type [{$type}] is invalid."
        );
    }

    /**
     * Set the actions for the handler.
     *
     * @param  array<int,Action>  $actions
     * @return $this
     */
    public function actions($actions)
    {
        $this->actions = $actions;

        return $this;
    }

    /**
     * Get the actions for the handler.
     *
     * @return array<int,Action>
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * Set the key to use for selecting records.
     *
     * @param  string|null  $key
     * @return $this
     */
    public function key($key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * Get the key to use for selecting records.
     *
     * @param  TBuilder  $builder
     * @return string
     */
    public function getKey($builder)
    {
        return $builder->qualifyColumn(
            $this->key ??= $builder->getModel()->getKeyName()
        );
    }

    /**
     * Handle the incoming action request using the actions from the source, and the resource provided.
     *
     * @param  Http\Requests\InvokableRequest  $request
     * @return Responsable|RedirectResponse
     */
    public function handle($request)
    {
        /** @var string */
        $type = $request->validated('type');

        $data = match ($type) {
            'inline' => InlineData::from($request),
            'bulk' => BulkData::from($request),
            'page' => ActionData::from($request),
            default => abort(400),
        };

        [$action, $query] = $this->resolveAction($type, $data);

        if (! $action || ! $query) {
            ActionNotFoundException::throw($data->name);
        }

        [$named, $typed] = Parameters::builder($query);

        if (! $action->isAllowed($named, $typed)) {
            ActionNotAllowedException::throw($data->name);
        }

        /** @var TModel|TBuilder $query */
        $result = $action->execute($query);

        if ($this->isResponsable($result)) {
            /** @var Responsable|RedirectResponse */
            return $result;
        }

        return back();
    }

    /**
     * Retrieve the action and query based on the type and data.
     *
     * @param  string  $type
     * @param  ActionData  $data
     * @return array{Action|null,TModel|TBuilder|null}
     */
    public function resolveAction($type, $data)
    {
        return match ($type) {
            'inline' => $this->resolveInlineAction(type($data)->as(InlineData::class)),
            'bulk' => $this->resolveBulkAction(type($data)->as(BulkData::class)),
            'page' => $this->resolvePageAction($data),
            default => InvalidActionException::throw($type),
        };
    }

    /**
     * Resolve the bulk action.
     *
     * @param  BulkData  $data
     * @return array{Action|null, TBuilder}
     */
    public function resolveBulkAction($data)
    {
        $resource = $this->getResource();
        $key = $this->getKey($resource);

        /** @var TBuilder $resource */
        $resource = $data->all
            ? $resource->whereNotIn($key, $data->except)
            : $resource->whereIn($key, $data->only);

        return [
            $this->getAction($data->name, BulkAction::class),
            $resource,
        ];
    }

    /**
     * Resolve the page action.
     *
     * @param  ActionData  $data
     * @return array{Action|null, TBuilder}
     */
    public function resolvePageAction($data)
    {
        return [
            $this->getAction($data->name, PageAction::class),
            $this->getResource(),
        ];
    }

    /**
     * Find the action by name and type.
     *
     * @param  string  $name
     * @param  class-string<Action>  $type
     * @return Action|null
     */
    public function getAction($name, $type)
    {
        return Arr::first(
            $this->getActions(),
            static fn (Action $action) => $action instanceof $type
                && $action->getName() === $name
        );
    }

    /**
     * Resolve the inline action.
     *
     * @param  InlineData  $data
     * @return array{Action|null, TModel|null}
     */
    protected function resolveInlineAction($data)
    {
        $resource = $this->getResource();
        $key = $this->getKey($resource);

        return [
            $this->getAction($data->name, InlineAction::class),
            $resource
                ->where($key, $data->record)
                ->first(),
        ];
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
