<?php

declare(strict_types=1);

namespace Honed\Action\Concerns;

use Honed\Action\Action;
use Honed\Action\BulkAction;
use Honed\Action\InlineAction;
use Honed\Action\PageAction;
use Illuminate\Support\Arr;

trait HasActions
{
    /**
     * List of the actions.
     *
     * @var array<int,\Honed\Action\Action>|null
     */
    protected $actions;

    /**
     * Whether to not provide inline actions.
     *
     * @var bool
     */
    protected $withoutInlineActions = false;

    /**
     * Whether to not provide bulk actions.
     *
     * @var bool
     */
    protected $withoutBulkActions = false;

    /**
     * Whether to not provide page actions.
     *
     * @var bool
     */
    protected $withoutPageActions = false;

    /**
     * Merge a set of actions with the existing.
     *
     * @param  iterable<int, \Honed\Action\Action>  ...$actions
     * @return $this
     */
    public function withActions(...$actions)
    {
        /** @var array<int, \Honed\Action\Action> */
        $actions = Arr::flatten($actions);

        $this->actions = \array_merge($this->actions ?? [], $actions);

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

        $actions = \method_exists($this, 'actions') ? $this->actions() : [];

        return \array_merge($actions, $this->actions ?? []);
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
     * Set the instance to not provide any actions.
     *
     * @param  bool  $withoutActions
     * @return $this
     */
    public function withoutActions($withoutActions = true)
    {
        $this->withoutInlineActions($withoutActions);
        $this->withoutBulkActions($withoutActions);
        $this->withoutPageActions($withoutActions);

        return $this;
    }

    /**
     * Determine if the instance should not provide any actions.
     *
     * @return bool
     */
    public function isWithoutActions()
    {
        return $this->isWithoutInlineActions() &&
            $this->isWithoutBulkActions() &&
            $this->isWithoutPageActions();
    }

    /**
     * Set the instance to not provide inline actions.
     *
     * @param  bool  $withoutInlineActions
     * @return $this
     */
    public function withoutInlineActions($withoutInlineActions = true)
    {
        $this->withoutInlineActions = $withoutInlineActions;

        return $this;
    }

    /**
     * Determine if the instance should not provide inline actions.
     *
     * @return bool
     */
    public function isWithoutInlineActions()
    {
        return $this->withoutInlineActions;
    }

    /**
     * Set the instance to not provide bulk actions.
     *
     * @param  bool  $withoutBulkActions
     * @return $this
     */
    public function withoutBulkActions($withoutBulkActions = true)
    {
        $this->withoutBulkActions = $withoutBulkActions;

        return $this;
    }

    /**
     * Determine if the instance should not provide bulk actions.
     *
     * @return bool
     */
    public function isWithoutBulkActions()
    {
        return $this->withoutBulkActions;
    }

    /**
     * Set the instance to not provide page actions.
     *
     * @param  bool  $withoutPageActions
     * @return $this
     */
    public function withoutPageActions($withoutPageActions = true)
    {
        $this->withoutPageActions = $withoutPageActions;

        return $this;
    }

    /**
     * Determine if the instance should not provide page actions.
     *
     * @return bool
     */
    public function isWithoutPageActions()
    {
        return $this->withoutPageActions;
    }

    /**
     * Retrieve only the inline actions.
     *
     * @return array<int,\Honed\Action\InlineAction>
     */
    public function getInlineActions()
    {
        if ($this->isWithoutInlineActions()) {
            return [];
        }

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
        if ($this->isWithoutBulkActions()) {
            return [];
        }

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
        if ($this->isWithoutPageActions()) {
            return [];
        }

        return \array_values(
            \array_filter(
                $this->getActions(),
                static fn (Action $action) => $action instanceof PageAction && $action->isAllowed()
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
}
