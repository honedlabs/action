<?php

declare(strict_types=1);

namespace Honed\Action\Concerns;

use Honed\Action\Action;
use Honed\Action\BulkAction;
use Honed\Action\InlineAction;
use Honed\Action\PageAction;

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
                static fn (Action $action) =>  $action instanceof InlineAction
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
                static fn (Action $action) => 
                    $action instanceof BulkAction && $action->isAllowed()
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
                static fn (Action $action) => 
                    $action instanceof PageAction && $action->isAllowed()
            )
        );
    }


    /**
     * Get the actions as an array.
     *
     * @return array<string,array<int,\Honed\Action\Action>|bool>
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
