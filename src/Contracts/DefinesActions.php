<?php

declare(strict_types=1);

namespace Honed\Action\Contracts;

use Illuminate\Support\Collection;

interface DefinesActions
{
    /**
     * Set the actions for the instance.
     *
     * @return array<int,\Honed\Action\Action>
     */
    public function actions(): array;

    /**
     * Determine if the instance has actions.
     */
    public function hasActions(): bool;

    /**
     * Get the inline actions for the instance.
     *
     * @return \Illuminate\Support\Collection<int,\Honed\Action\InlineAction>
     */
    public function inlineActions(): Collection;

    /**
     * Get the bulk actions for the instance.
     *
     * @return \Illuminate\Support\Collection<int,\Honed\Action\BulkAction>
     */
    public function bulkActions(): Collection;
}
