<?php

declare(strict_types=1);

namespace Honed\Action;

use Honed\Action\Contracts\DefinesActions;
use Honed\Action\Exceptions\InvalidActionException;
use Honed\Action\Http\Data\BulkData;
use Honed\Action\Http\Data\InlineData;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 */
class ActionHandler
{
    /**
     * @param  \Illuminate\Database\Eloquent\Builder<TModel>  $resource
     */
    public function __construct(
        protected DefinesActions $source,
        protected Builder $resource,
        protected ?string $findBy = null,
        protected bool $tablePrefix = false
    ) {}

    /**
     * Handle the incoming action request using the actions from the source, and the resource provided.
     *
     * @param  \Honed\Action\Http\Requests\ActionRequest  $request
     * @return \Illuminate\Contracts\Support\Responsable|\Illuminate\Http\RedirectResponse|void
     */
    public function handle($request)
    {
        /**
         * @var string
         */
        $type = $request->validated('type');

        $data = match ($type) {
            Creator::Inline => InlineData::from($request),
            Creator::Bulk => BulkData::from($request),
            // Creator::Page => Inline
            default => abort(400),
        };

        [$action, $query] = $this->resolveAction($type, $data);

        if (\is_null($action)) {
            return abort(400);
        }

        if ($action instanceof InlineAction && ! $action->allows($query)) {
            return abort(403);
        }

        $result = $action->execute($query);

        if ($result instanceof Responsable || $result instanceof Response) {
            return $result;
        }

        return back();
    }

    /**
     * Retrieve the action and query based on the type and data.
     * 
     * @return array{0: \Honed\Action\Action|null, 1: \Illuminate\Database\Eloquent\Builder<TModel>|TModel}
     */
    private function resolveAction(string $type, InlineData|BulkData $data): array
    {
        $query = $this->resource;

        return match ($type) {
            Creator::Inline => [
                $this->source->inlineActions()
                    ->first(fn (InlineAction $action) => $action->getName() === $data->name),

                $query->where($this->column(), $data->id)->first(),
            ],

            Creator::Bulk => [
                $this->source->bulkActions()
                    ->first(fn (BulkAction $action) => $action->getName() === $data->name),

                $data->all
                    ? $query->whereNotIn($this->column(), $data->except)
                    : $query->whereIn($this->column(), $data->only),
            ],

            default => throw new InvalidActionException($type),
        };
    }

    /**
     * Get the model for this resource.
     * 
     * @return TModel
     */
    protected function model()
    {
        return $this->resource->getModel();
    }

    /**
     * Retrieve the key to use for selecting records.
     */
    protected function key(): string
    {
        return $this->findBy ??= $this->model()->getKeyName();
    }

    /**
     * Retrieve the namespaced column to use for selecting records.
     */
    protected function column(): string
    {
        return $this->tablePrefix
            ? $this->model()->getTable().'.'.$this->key()
            : $this->key();
    }
}
