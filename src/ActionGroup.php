<?php

declare(strict_types=1);

namespace Honed\Action;

use Honed\Action\Concerns\HasActions;
use Honed\Core\Primitive;

class ActionGroup extends Primitive
{
    use HasActions;

    /**
     * Create a new action group instance.
     *
     * @param  \Honed\Action\PageAction  ...$actions
     */
    public static function make(...$actions): static
    {
        return resolve(static::class)
            ->withActions($actions);
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return $this->pageActionsToArray();
    }
}
