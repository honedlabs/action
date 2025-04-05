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
     * @var array<int,\Honed\Action\Action|\Honed\Action\ActionGroup>
     */
    protected $actions = [];

    /**
     * Whether to provide inline actions.
     *
     * @var bool
     */
    protected $inline = true;

    /**
     * Whether to provide bulk actions.
     *
     * @var bool
     */
    protected $bulk = true;

    /**
     * Whether to provide page actions.
     *
     * @var bool
     */
    protected $page = true;

    /**
     * Merge a set of actions with the existing.
     *
     * @param  \Honed\Action\Action|\Honed\Action\ActionGroup|iterable<int, \Honed\Action\Action|\Honed\Action\ActionGroup>  ...$actions
     * @return $this
     */
    public function actions(...$actions)
    {
        /** @var array<int, \Honed\Action\Action> */
        $actions = Arr::flatten($actions);

        $this->actions = \array_merge($this->actions, $actions);

        return $this;
    }

    /**
     * Define the actions for the instance.
     *
     * @return array<int,\Honed\Action\Action|\Honed\Action\ActionGroup>
     */
    public function defineActions()
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
        $actions = \array_merge(
            $this->defineActions(),
            $this->actions
        );

        return \array_merge(
            [],
            ...\array_map(
                static fn ($action) => $action instanceof ActionGroup
                    ? $action->getActions()
                    : [$action],
                $actions
            )
        );
    }

    /**
     * Enable all action types.
     *
     * @return $this
     */
    public function allActions()
    {
        $this->inline = true;
        $this->bulk = true;
        $this->page = true;

        return $this;
    }

    /**
     * Disable all action types.
     *
     * @return $this
     */
    public function exceptActions()
    {
        $this->exceptInlineActions();
        $this->exceptBulkActions();
        $this->exceptPageActions();

        return $this;
    }

    /**
     * Determine if the instance has provided all actions.
     *
     * @return bool
     */
    public function hasAllActions()
    {
        return $this->inline && $this->bulk && $this->page;
    }

    /**
     * Determine if the instance provides any actions.
     *
     * @return bool
     */
    public function hasActions()
    {
        return $this->inline || $this->bulk || $this->page;
    }

    /**
     * Determine if the instance has not provided any actions.
     *
     * @return bool
     */
    public function hasNoActions()
    {
        return ! $this->hasActions();
    }

    /**
     * Set the instance to only provide inline actions.
     *
     * @return $this
     */
    public function onlyInlineActions()
    {
        $this->inline = true;
        $this->bulk = false;
        $this->page = false;

        return $this;
    }

    /**
     * Set the instance to not provide inline actions.
     *
     * @return $this
     */
    public function exceptInlineActions()
    {
        $this->inline = false;

        return $this;
    }

    /**
     * Determine if the instance provides inline actions.
     *
     * @return bool
     */
    public function hasInlineActions()
    {
        return $this->inline;
    }

    /**
     * Set the instance to only provide bulk actions.
     *
     * @return $this
     */
    public function onlyBulkActions()
    {
        $this->inline = false;
        $this->bulk = true;
        $this->page = false;

        return $this;
    }

    /**
     * Set the instance to not provide bulk actions.
     *
     * @return $this
     */
    public function exceptBulkActions()
    {
        $this->bulk = false;

        return $this;
    }

    /**
     * Determine if the instance provides bulk actions.
     *
     * @return bool
     */
    public function hasBulkActions()
    {
        return $this->bulk;
    }

    /**
     * Set the instance to only provide page actions.
     *
     * @return $this
     */
    public function onlyPageActions()
    {
        $this->inline = false;
        $this->bulk = false;
        $this->page = true;

        return $this;
    }

    /**
     * Set the instance to not provide page actions.
     *
     * @return $this
     */
    public function exceptPageActions()
    {
        $this->page = false;

        return $this;
    }

    /**
     * Determine if the instance provides page actions.
     *
     * @return bool
     */
    public function hasPageActions()
    {
        return $this->page;
    }

    /**
     * Retrieve only the allowed inline actions.
     *
     * @return array<int,\Honed\Action\InlineAction>
     */
    public function getInlineActions()
    {
        if (! $this->hasInlineActions()) {
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
        if (! $this->hasBulkActions()) {
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
        if (! $this->hasPageActions()) {
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
        $named = $typed = [];

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
