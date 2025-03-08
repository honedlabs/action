<?php

declare(strict_types=1);

namespace Honed\Action;

use Honed\Action\Http\Data\ActionData;
use Honed\Action\Http\Data\BulkData;
use Honed\Action\Http\Data\InlineData;
use Honed\Core\Concerns\HasBuilderInstance;
use Honed\Core\Contracts\Makeable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 * @template TBuilder of \Illuminate\Database\Eloquent\Builder
 */
class Handler implements Makeable
{
    use Concerns\HasParameterNames;

    /**
     * @use HasBuilderInstance<TBuilder, TModel>
     */
    use HasBuilderInstance;

    /**
     * @var array<int,\Honed\Action\Action>
     */
    protected $actions = [];

    /**
     * @var string|null
     */
    protected $key;

    /**
     * Create a new handler instance.
     *
     * @param  TBuilder  $builder
     * @param  array<int,\Honed\Action\Action>  $actions
     * @param  string|null  $key
     */
    public function __construct($builder, $actions, $key = null)
    {
        $this->builder = $builder;
        $this->actions = $actions;
        $this->key = $key;
    }

    /**
     * Make a new handler instance.
     *
     * @param  TBuilder  $builder
     * @param  array<int,\Honed\Action\Action>  $actions
     * @param  string|null  $key
     * @return static
     */
    public static function make($builder, $actions, $key = null)
    {
        return resolve(static::class, \compact('builder', 'actions', 'key'));
    }

    /**
     * Handle the incoming action request using the actions from the source, and the resource provided.
     *
     * @param  \Honed\Action\Http\Requests\ActionRequest  $request
     * @return \Illuminate\Contracts\Support\Responsable|\Symfony\Component\HttpFoundation\RedirectResponse|void
     */
    public function handle($request)
    {
        /** @var string */
        $type = $request->validated('type');

        $data = match ($type) {
            ActionFactory::Inline => InlineData::from($request),
            ActionFactory::Bulk => BulkData::from($request),
            ActionFactory::Page => ActionData::from($request),
            default => abort(400),
        };

        [$action, $query] = $this->resolveAction($type, $data);

        abort_if(\is_null($action), 400);

        abort_if(\is_null($query), 404);

        abort_if(! $action->isAllowed(...static::getNamedAndTypedParameters($query)), 403);

        /** @var TBuilder|TModel $query */
        $result = $action->execute($query);

        if ($result instanceof Responsable || $result instanceof RedirectResponse) {
            return $result;
        }

        return back();
    }

    /**
     * Retrieve the action and query based on the type and data.
     *
     * @param  string  $type
     * @param  \Honed\Action\Http\Data\ActionData  $data
     * @return array{0: \Honed\Action\Action|null, 1: TBuilder<TModel>|TModel|null}
     */
    protected function resolveAction($type, $data)
    {
        return match ($type) {
            ActionFactory::Inline => $this->resolveInlineAction(type($data)->as(InlineData::class)),
            ActionFactory::Bulk => $this->resolveBulkAction(type($data)->as(BulkData::class)),
            ActionFactory::Page => $this->resolvePageAction($data),
            default => static::throwInvalidArgumentException($type),
        };
    }

    /**
     * Get the actions for the handler.
     *
     * @return array<int,\Honed\Action\Action>
     */
    protected function getActions()
    {
        return $this->actions;
    }

    /**
     * Get the key to use for selecting records.
     *
     * @param  TBuilder<TModel>  $builder
     * @return string
     */
    protected function getKey($builder)
    {
        return $builder->qualifyColumn($this->key
            ??= $builder->getModel()->getKeyName());
    }

    /**
     * Resolve the inline action.
     *
     * @param  \Honed\Action\Http\Data\InlineData  $data
     * @return array{0: \Honed\Action\Action|null, 1: TModel|null}
     */
    protected function resolveInlineAction($data)
    {
        return [
            $this->getAction($data->name, InlineAction::class),
            $this->getBuilder()
                ->where($this->getKey($this->getBuilder()), $data->id)
                ->first(),
        ];
    }

    /**
     * Resolve the bulk action.
     *
     * @param  \Honed\Action\Http\Data\BulkData  $data
     * @return array{0: \Honed\Action\Action|null, 1: TBuilder<TModel>}
     */
    protected function resolveBulkAction($data)
    {
        $builder = $this->getBuilder();

        $key = $this->getKey($builder);

        return [
            $this->getAction($data->name, BulkAction::class),
            $data->all
                ? $builder->whereNotIn($key, $data->except)
                : $builder->whereIn($key, $data->only),
        ];
    }

    /**
     * Resolve the page action.
     *
     * @param  \Honed\Action\Http\Data\ActionData  $data
     * @return array{0: \Honed\Action\Action|null, 1: TBuilder<TModel>}
     */
    protected function resolvePageAction($data)
    {
        return [
            $this->getAction($data->name, PageAction::class),
            $this->getBuilder(),
        ];
    }

    /**
     * Throw an invalid argument exception.
     *
     * @param  string  $type
     * @return never
     */
    protected static function throwInvalidArgumentException($type)
    {
        throw new \InvalidArgumentException(\sprintf(
            'Action type [%s] is invalid.', $type
        ));
    }

    /**
     * Find the action by name and type.
     *
     * @param  string  $name
     * @param  string  $type
     * @return \Honed\Action\Action|null
     */
    protected function getAction($name, $type)
    {
        return Arr::first(
            $this->getActions(),
            fn (Action $action) => $action instanceof $type
                && $action->getName() === $name
        );
    }
}
