<?php

declare(strict_types=1);

namespace Honed\Action\Concerns;

use Honed\Action\Action;
use Honed\Action\BulkAction;
use Honed\Action\InlineAction;
use Honed\Action\PageAction;
use Illuminate\Support\Collection;

trait HasActions
{
    /**
     * List of the actions.
     *
     * @var array<int,\Honed\Action\Action>|null
     */
    protected $actions;

    /**
     * Whether the actions should be retrievable.
     *
     * @var bool
     */
    protected $withoutActions = false;

    /**
     * Merge a set of actions with the existing.
     *
     * @param  array<int, \Honed\Action\Action>|\Illuminate\Support\Collection<int, \Honed\Action\Action>  $actions
     * @return $this
     */
    public function addActions($actions)
    {
        if ($actions instanceof Collection) {
            $actions = $actions->all();
        }

        $this->actions = \array_merge($this->actions ?? [], $actions);

        return $this;
    }

    /**
     * Add a single action to the list of actions.
     *
     * @param  \Honed\Action\Action  $action
     * @return $this
     */
    public function addAction($action)
    {
        $this->actions[] = $action;

        return $this;
    }

    /**
     * Retrieve the actions
     *
     * @return array<int,\Honed\Action\Action>
     */
    public function getActions()
    {
        if ($this->isWithoutActions()) {
            return [];
        }

        $methodFilters = \method_exists($this, 'actions')
            ? $this->actions()
            : [];

        $propertyFilters = $this->actions ?? [];

        return \array_merge($methodFilters, $propertyFilters);
    }

    /**
     * Determine if the instance has any actions.
     *
     * @return bool
     */
    public function hasActions()
    {
        return filled($this->getActions());
    }

    /**
     * Set the actions to not be retrieved.
     *
     * @return $this
     */
    public function withoutActions()
    {
        $this->withoutActions = true;

        return $this;
    }

    /**
     * Determine if the actions should not be retrieved.
     *
     * @return bool
     */
    public function isWithoutActions()
    {
        return $this->withoutActions;
    }

    /**
     * Retrieve only the inline actions.
     *
     * @return array<int,\Honed\Action\InlineAction>
     */
    public function getInlineActions()
    {
        return \array_values(
            \array_filter(
                $this->getActions(),
                static fn (Action $action) => $action instanceof InlineAction
            )
        );
    }

    /**
     * Retrieve only the allowed bulk actions.
     *
     * @return array<int,\Honed\Action\BulkAction>
     */
    public function getBulkActions()
    {
        return \array_values(
            \array_filter(
                $this->getActions(),
                static fn (Action $action) => $action instanceof BulkAction && $action->isAllowed()
            )
        );
    }

    /**
     * Retrieve only the allowed page actions.
     *
     * @return array<int,\Honed\Action\PageAction>
     */
    public function getPageActions()
    {
        return \array_values(
            \array_filter(
                $this->getActions(),
                static fn (Action $action) => $action instanceof PageAction && $action->isAllowed()
            )
        );
    }

    /**
     * Get the actions as an array.
     *
     * @return array<string,mixed>
     */
    public function actionsToArray()
    {
        return [
            'hasInline' => filled($this->getInlineActions()),
            'bulk' => $this->bulkActionsToArray(),
            'page' => $this->pageActionsToArray(),
        ];
    }

    /**
     * Get the bulk actions as an array.
     *
     * @return array<int,mixed>
     */
    public function bulkActionsToArray()
    {
        return \array_map(
            static fn (BulkAction $action) => $action->toArray(),
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
        return \array_map(
            static fn (PageAction $action) => $action->toArray(),
            $this->getPageActions()
        );
    }

    /**
     * Get the actions for a record.
     *
     * @param  array<string,mixed>  $named
     * @param  array<class-string,mixed>  $typed
     * @return array<int,mixed>
     */
    public function getRecordActions($named, $typed)
    {
        $allowed = \array_filter(
            $this->getInlineActions(),
            static fn (InlineAction $action) => $action->isAllowed($named, $typed)
        );

        return \array_map(
            static fn (InlineAction $action) => $action->resolve($named, $typed)->toArray(),
            $allowed
        );
    }
}
