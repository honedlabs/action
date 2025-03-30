<?php

declare(strict_types=1);

namespace Honed\Action\Concerns;

use Honed\Action\Action;
use Honed\Action\ActionGroup;
use Honed\Action\BulkAction;
use Honed\Action\InlineAction;
use Honed\Action\PageAction;
use Honed\Core\Parameters;
use Illuminate\Support\Arr;

trait HasActions
{
    /**
     * List of the actions.
     *
     * @var array<int,\Honed\Action\Action|\Honed\Action\ActionGroup>|null
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
     * @param  \Honed\Action\Action|\Honed\Action\ActionGroup|iterable<int, \Honed\Action\Action|\Honed\Action\ActionGroup>  ...$actions
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
     * Define the actions for the instance.
     *
     * @return array<int,\Honed\Action\Action|\Honed\Action\ActionGroup>
     */
    public function actions()
    {
        return [];
    }

    /**
     * Retrieve the actions
     *
     * @return array<int,\Honed\Action\Action>
     */
    public function getActions()
    {
        return \array_merge(
            [],
            ...\array_map(
                static fn ($action) => $action instanceof ActionGroup
                    ? $action->getActions()
                    : [$action],
                \array_merge($this->actions(), $this->actions ?? [])
            )
        );
    }

    /**
     * Disable all action types.
     *
     * @return $this
     */
    public function withoutActions(bool $without = true)
    {
        $this->withoutInlineActions($without);
        $this->withoutBulkActions($without);
        $this->withoutPageActions($without);

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
                static fn (Action $action) => $action instanceof BulkAction &&
                    $action->isAllowed()
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
                static fn (Action $action) => $action instanceof PageAction &&
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
        $named = [];
        $typed = [];

        if ($model) {
            [$named, $typed] = Parameters::model($model);
        }

        return \array_map(
            static fn (InlineAction $action) => $action
                ->resolveToArray($named, $typed),
            \array_values(
                \array_filter(
                    $this->getInlineActions(),
                    static fn (InlineAction $action) => $action->isAllowed($named, $typed)
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
}
