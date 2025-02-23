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
    public $actions;

    /**
     * Retrieve the actions
     *
     * @return array<int,\Honed\Action\Action>
     */
    public function getActions(): array
    {
        return match (true) {
            isset($this->actions) => $this->actions,
            \method_exists($this, 'actions') => $this->actions(),
            default => [],
        };
    }

    /**
     * Add a list of actions to the instance.
     *
     * @param  iterable<\Honed\Action\Action>  $actions
     * @return $this
     */
    public function addActions(iterable $actions): static
    {
        if ($actions instanceof Collection) {
            $actions = $actions->all();
        }

        /**
         * @var array<int, \Honed\Action\Action> $actions
         */
        $this->actions = \array_merge($this->actions ?? [], $actions);

        return $this;
    }

    /**
     * Add a single action to the instance.
     *
     * @return $this
     */
    public function addAction(Action $action): static
    {
        $this->actions[] = $action;

        return $this;
    }

    /**
     * Determine if the instance has any actions.
     */
    public function hasActions(): bool
    {
        return filled($this->getActions());
    }

    /**
     * Retrieve only the inline actions.
     *
     * @return array<int,\Honed\Action\InlineAction>
     */
    public function getInlineActions(): array
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
    public function getBulkActions(): array
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
    public function getPageActions(): array
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
    public function actionsToArray(): array
    {
        return [
            'actions' => filled($this->getInlineActions()),
            'bulk' => $this->bulkActionsToArray(),
            'page' => $this->pageActionsToArray(),
        ];
    }

    /**
     * Get the bulk actions as an array.
     *
     * @return array<int,mixed>
     */
    public function bulkActionsToArray(): array
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
    public function pageActionsToArray(): array
    {
        return \array_map(
            static fn (PageAction $action) => $action->toArray(),
            $this->getPageActions()
        );
    }
}
