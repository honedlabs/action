<?php

declare(strict_types=1);

namespace Honed\Action\Concerns;

use Honed\Action\BulkAction;
use Honed\Action\InlineAction;
use Honed\Action\PageAction;

trait HasActions
{
    /**
     * @var array<int,\Honed\Action\Action>
     */
    public $actions;

    /**
     * Get the actions for the instance.
     *
     * @return array<int,\Honed\Action\Action>
     */
    public function getActions(): array
    {
        return match (true) {
            \property_exists($this, 'actions') && ! \is_null($this->actions) => $this->actions,
            \method_exists($this, 'actions') => $this->actions(),
            default => [],
        };
    }

    /**
     * Determine if the instance has actions.
     */
    public function hasActions(): bool
    {
        return \count($this->getActions()) > 0;
    }

    /**
     * Get the inline actions for the instance.
     *
     * @return array<int,\Honed\Action\InlineAction>
     */
    public function inlineActions(): array
    {
        return \array_filter($this->getActions(), static fn ($action) => $action instanceof InlineAction);
    }

    /**
     * Get the bulk actions for the instance.
     *
     * @return array<int,\Honed\Action\BulkAction>
     */
    public function bulkActions(): array
    {
        return \array_values(
            \array_filter($this->getActions(), static fn ($action) => $action instanceof BulkAction && $action->isAllowed())
        );
    }

    /**
     * Get the page actions for the instance.
     *
     * @return array<int,\Honed\Action\PageAction>
     */
    public function pageActions(): array
    {
        return \array_values(
            \array_filter($this->getActions(), static fn ($action) => $action instanceof PageAction && $action->isAllowed())
        );
    }

    /**
     * Convert the independent actions to an array.
     *
     * @return array<string,array<int,\Honed\Action\Action>|bool>
     */
    public function actionsToArray(): array
    {
        return [
            'actions' => \count($this->inlineActions()) > 0,
            'bulk' => $this->bulkActions(),
            'page' => $this->pageActions(),
        ];
    }
}
