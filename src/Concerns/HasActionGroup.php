<?php

declare(strict_types=1);

namespace Honed\Action\Concerns;

use Honed\Action\ActionGroup;
use Honed\Action\Attributes\Actions;

/**
 * @template TActions of \Honed\Action\ActionGroup
 *
 * @property string $actions
 */
trait HasActionGroup
{
    /**
     * Get the action group instance for the model.
     *
     * @return TActionGroup
     */
    public static function actions()
    {
        return static::newActions()
            ?? ActionGroup::actionsForModel(static::class);
    }

    /**
     * Create a new action group instance for the model.
     *
     * @return TActions
     */
    protected static function newActions()
    {
        if (isset(static::$actions)) {
            return static::$actions::make();
        }

        if ($actions = static::getActionsAttribute()) {
            return $actions::make();
        }

        return null;
    }

    /**
     * Get the table from the Table class attribute.
     *
     * @return class-string<\Honed\Table\Table>|null
     */
    protected static function getTableAttribute()
    {
        $attributes = (new \ReflectionClass(static::class))
            ->getAttributes(Actions::class);

        if ($attributes !== []) {
            $group = $attributes[0]->newInstance();

            return $group->actions;
        }

        return null;
    }
}
