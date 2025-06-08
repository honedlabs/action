<?php

declare(strict_types=1);

namespace Honed\Action\Concerns;

use Honed\Action\Action;
use Honed\Action\ActionGroup;
use Honed\Core\Parameters;
use Illuminate\Support\Arr;

use function array_filter;
use function array_map;
use function array_merge;
use function array_values;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model = \Illuminate\Database\Eloquent\Model
 * @template TBuilder of \Illuminate\Database\Eloquent\Builder<TModel> = \Illuminate\Database\Eloquent\Builder<TModel>
 *
 * @phpstan-require-extends \Honed\Core\Primitive
 */
trait HandlesActions
{
    /**
     * Whether the instance should provide inline actions.
     *
     * @var bool
     */
    protected $inline = true;

    /**
     * Whether the instance should provide bulk actions.
     *
     * @var bool
     */
    protected $bulk = true;

    /**
     * Whether the instance should provide page actions.
     *
     * @var bool
     */
    protected $page = true;

    /**
     * List of the actions.
     *
     * @var array<int,Action|ActionGroup<TModel, TBuilder>>
     */
    protected $actions = [];

    /**
     * Merge a set of actions with the existing.
     *
     * @param  Action|ActionGroup<TModel, TBuilder>|iterable<int, Action|ActionGroup<TModel, TBuilder>>  ...$actions
     * @return $this
     */
    public function withActions(...$actions)
    {
        /** @var array<int, Action> */
        $actions = Arr::flatten($actions);

        $this->actions = array_merge($this->actions, $actions);

        return $this;
    }

    /**
     * Define the actions for the instance.
     *
     * @return array<int,Action|ActionGroup<TModel, TBuilder>>
     */
    public function actions()
    {
        return [];
    }

    /**
     * Retrieve the actions
     *
     * @return array<int,Action>
     */
    public function getActions()
    {
        $actions = array_merge(
            $this->actions(),
            $this->actions
        );

        return array_merge(
            [],
            ...array_map(
                static fn ($action) => $action instanceof ActionGroup
                    ? $action->getActions()
                    : [$action],
                $actions
            )
        );
    }

    /**
     * Determines if the instance has any actions.
     *
     * @return bool
     */
    public function hasActions()
    {
        return filled($this->getActions());
    }

    /**
     * Set whether the instance should provide actions.
     *
     * @param  bool  $show
     * @return $this
     */
    public function showActions($show = true)
    {
        $this->inline = $show;
        $this->bulk = $show;
        $this->page = $show;

        return $this;
    }

    /**
     * Determine if the instance provides any actions.
     *
     * @return bool
     */
    public function showsActions()
    {
        return $this->inline || $this->bulk || $this->page;
    }

    /**
     * Determine if the instance hides all actions.
     *
     * @return bool
     */
    public function hidesActions()
    {
        return ! $this->showsActions();
    }

    /**
     * Set whether the instance should provide inline actions.
     *
     * @param  bool  $show
     * @return $this
     */
    public function showInlineActions($show = true)
    {
        $this->inline = $show;

        return $this;
    }

    /**
     * Set whether the instance should hide inline actions.
     *
     * @param  bool  $hide
     * @return $this
     */
    public function hideInlineActions($hide = true)
    {
        return $this->showInlineActions(! $hide);
    }

    /**
     * Determine if the instance provides inline actions.
     *
     * @return bool
     */
    public function showsInlineActions()
    {
        return $this->inline;
    }

    /**
     * Determine if the instance hides inline actions.
     *
     * @return bool
     */
    public function hidesInlineActions()
    {
        return ! $this->showsInlineActions();
    }

    /**
     * Set whether the instance should provide bulk actions.
     *
     * @param  bool  $show
     * @return $this
     */
    public function showBulkActions($show = true)
    {
        $this->bulk = $show;

        return $this;
    }

    /**
     * Set whether the instance should hide bulk actions.
     *
     * @param  bool  $hide
     * @return $this
     */
    public function hideBulkActions($hide = true)
    {
        return $this->showBulkActions(! $hide);
    }

    /**
     * Determine if the instance provides bulk actions.
     *
     * @return bool
     */
    public function showsBulkActions()
    {
        return $this->bulk;
    }

    /**
     * Determine if the instance hides bulk actions.
     *
     * @return bool
     */
    public function hidesBulkActions()
    {
        return ! $this->showsBulkActions();
    }

    /**
     * Set whether the instance should provide page actions.
     *
     * @param  bool  $show
     * @return $this
     */
    public function showPageActions($show = true)
    {
        $this->page = $show;

        return $this;
    }

    /**
     * Set whether the instance should hide page actions.
     *
     * @param  bool  $hide
     * @return $this
     */
    public function hidePageActions($hide = true)
    {
        return $this->showPageActions(! $hide);
    }

    /**
     * Determine if the instance provides page actions.
     *
     * @return bool
     */
    public function showsPageActions()
    {
        return $this->page;
    }

    /**
     * Determine if the instance hides page actions.
     *
     * @return bool
     */
    public function hidesPageActions()
    {
        return ! $this->showsPageActions();
    }

    /**
     * Retrieve only the allowed inline actions.
     *
     * @return array<int,Action>
     */
    public function getInlineActions()
    {
        if ($this->hidesInlineActions()) {
            return [];
        }

        return array_values(
            array_filter(
                $this->getActions(),
                static fn (Action $action) => $action->isInline()
            )
        );
    }

    /**
     * Retrieve only the allowed bulk actions.
     *
     * @return array<int,Action>
     */
    public function getBulkActions()
    {
        if ($this->hidesBulkActions()) {
            return [];
        }

        return array_values(
            array_filter(
                $this->getActions(),
                static fn (Action $action) => $action->isBulk() &&
                    $action->isAllowed()
            )
        );
    }

    /**
     * Retrieve only the allowed page actions.
     *
     * @return array<int,Action>
     */
    public function getPageActions()
    {
        if ($this->hidesPageActions()) {
            return [];
        }

        return array_values(
            array_filter(
                $this->getActions(),
                static fn (Action $action) => $action->isPage() &&
                    $action->isAllowed()
            )
        );
    }

    /**
     * Get the inline actions as an array.
     *
     * @param  \Illuminate\Database\Eloquent\Model|null  $model
     * @return array<int,mixed>
     */
    public function inlineActionsToArray($model = null)
    {
        $named = $typed = [];

        if ($model) {
            [$named, $typed] = Parameters::model($model);
        }

        return array_map(
            static fn (Action $action) => $action
                ->toArray($named, $typed),
            array_values(
                array_filter(
                    $this->getInlineActions(),
                    static fn (Action $action) => $action->isAllowed($named, $typed)
                )
            )
        );
    }

    /**
     * Get the bulk actions as an array.
     *
     * @return array<int,mixed>
     */
    public function bulkActionsToArray()
    {
        return array_map(
            static fn (Action $action) => $action->toArray(),
            $this->getBulkActions()
        );
    }

    /**
     * Get the page actions as an array.
     *
     * @return array<int,mixed>
     */
    public function pageActionsToArray()
    {
        return array_map(
            static fn (Action $action) => $action->toArray(),
            $this->getPageActions()
        );
    }
}
