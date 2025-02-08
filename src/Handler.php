<?php

declare(strict_types=1);

namespace Honed\Action;

use Honed\Action\Http\Data\BulkData;
use Honed\Action\Http\Data\PageData;
use Honed\Action\Http\Data\ActionData;
use Honed\Action\Http\Data\InlineData;
use Illuminate\Database\Eloquent\Model;
use Honed\Core\Contracts\TransferObject;
use Illuminate\Database\Eloquent\Builder;
use Honed\Core\Concerns\HasBuilderInstance;
use Honed\Action\Concerns\HasParameterNames;
use Illuminate\Contracts\Support\Responsable;
use Honed\Action\Exceptions\InvalidActionException;
use Honed\Core\Concerns\RequiresKey;
use Honed\Core\Contracts\Makeable;
use Symfony\Component\HttpFoundation\RedirectResponse;

class Handler implements Makeable
{
    use HasBuilderInstance;
    use Concerns\HasParameterNames;

    /**
     * @var array<int,\Honed\Action\Action>
     */
    protected $actions = [];

    /**
     * @var string|null
     */
    protected $key;

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>  $builder
     * @param  array<int,\Honed\Action\Action>  $actions
     */
    public function __construct(Builder $builder, array $actions, string $key = null)
    {
        $this->builder = $builder;
        $this->actions = $actions;
        $this->key = $key;
    }

    /**
     * Make a new handler instance.
     * 
     * @param  \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>  $builder
     * @param  array<int,\Honed\Action\Action>  $actions
     */
    public static function make(Builder $builder, array $actions, string $key = null): static
    {
        return resolve(static::class, \compact('builder', 'actions', 'key'));
    }

    /**
     * Handle the incoming action request using the actions from the source, and the resource provided.
     *
     * @param  \Honed\Action\Http\Requests\ActionRequest  $request
     * @return \Illuminate\Contracts\Support\Responsable|\Illuminate\Http\RedirectResponse|void
     */
    public function handle($request)
    {
        /** @var string */
        $type = $request->validated('type');

        $data = match ($type) {
            Creator::Inline => InlineData::from($request),
            Creator::Bulk => BulkData::from($request),
            Creator::Page => ActionData::from($request),
            default => abort(400),
        };

        [$action, $query] = $this->resolveAction($type, $data);

        abort_if(\is_null($action), 400);

        $inline = $action instanceof InlineAction;

        abort_if($inline && \is_null($query), 404);

        $this->checkValidity($action, $query);

        /** @var \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>|\Illuminate\Database\Eloquent\Model $query */
        $result = $action->execute($query);

        if ($result instanceof Responsable || $result instanceof RedirectResponse) {
            return $result;
        }

        return back();
    }

    /**
     * Retrieve the action and query based on the type and data.
     * 
     * @return array{0: \Honed\Action\Action|null, 1: \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>|\Illuminate\Database\Eloquent\Model|null}
     */
    protected function resolveAction(string $type, ActionData $data): array
    {
        return match ($type) {
            Creator::Inline => $this->resolveInlineAction(type($data)->as(InlineData::class)),
            Creator::Bulk => $this->resolveBulkAction(type($data)->as(BulkData::class)),
            Creator::Page => $this->resolvePageAction($data),
            default => throw new InvalidActionException($type),
        };
    }

    /**
     * Get the actions for the handler.
     * 
     * @return array<int,\Honed\Action\Action>
     */
    protected function getActions(): array
    {
        return $this->actions;
    }

    /**
     * Get the key to use for selecting records.
     * 
     * @param \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model> $builder
     */
    protected function getKey(Builder $builder): string
    {
        return $builder->qualifyColumn($this->key ??= $builder->getModel()->getKeyName());
    }

    /**
     * @return array{\Honed\Action\Action|null,\Illuminate\Database\Eloquent\Model|null}
     */
    protected function resolveInlineAction(InlineData $data): array
    {
        return [
            collect($this->getActions())->first(fn (Action $action) => $action instanceof InlineAction && $action->getName() === $data->name),
            $this->getBuilder()->where($this->getKey($this->getBuilder()), $data->id)->first(),
        ];
    }

    /**
     * @return array{\Honed\Action\Action|null,\Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>}
     */
    protected function resolveBulkAction(BulkData $data): array
    {
        $builder = $this->getBuilder();
        
        $key = $this->getKey($builder);

        return [
            collect($this->getActions())->first(fn (Action $action) => $action instanceof BulkAction && $action->getName() === $data->name),
            $data->all ? $builder->whereNotIn($key, $data->except) : $builder->whereIn($key, $data->only),
        ];
    }

    /**
     * @return array{\Honed\Action\Action|null,\Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>}
     */
    protected function resolvePageAction(ActionData $data): array
    {
        return [
            collect($this->getActions())->first(fn (Action $action) => $action instanceof PageAction && $action->getName() === $data->name),
            $this->getBuilder(),
        ];
    }

    /**
     * Check whether the action is valid and the current user can execute it.
     * 
     * @param \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>|\Illuminate\Database\Eloquent\Model|null $query
     */
    protected function checkValidity(?Action $action, $query): void
    {
        abort_if(\is_null($action), 400);

        abort_if(\is_null($query), 404);

        [$model, $singular, $plural] = $this->getParameterNames($query);

        $named = [
            'model' => $query,
            'record' => $query,
            'query' => $query,
            'builder' => $query,
            $singular => $query,
            $plural => $query,
        ];

        $typed = [
            Model::class => $query,
            Builder::class => $query,
            $model::class => $query,
        ];

        abort_if(! $action->isAllowed($named, $typed), 403);
    }
}
