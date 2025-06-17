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
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Redirect;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @template TClass of \Honed\Action\Contracts\HandlesActions
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
     * @return array<int,Action>
     */
    abstract protected function getActions();

    /**
     * Get the query builder to be used to retrieve resources.
     *
     * @return \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>
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
     * @param  Http\Requests\InvokableRequest  $request
     * @return Responsable|RedirectResponse
     */
    protected function resolve($request)
    {
        /** @var string */
        $type = $request->validated('type');

        $response = match ($type) {

            Action::INLINE => $this->handleInlineAction($request),
            Action::BULK => $this->handleBulkAction($request),
            Action::PAGE => $this->handlePageAction($request),
            default => abort(400, 'Invalid action type.'),
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
            return $result;
        }

        return Redirect::back();
    }

    /**
     * Retrieve the action and query based on the type and data.
     *
     * @param  string  $type
     * @param  ActionData  $data
     * @return array{Action|null,TModel|TBuilder|null}
     */
    protected function resolveAction($type, $data)
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
    protected function resolveBulkAction($data)
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
    protected function resolvePageAction($data)
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
    protected function getAction($name, $type)
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
