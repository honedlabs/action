<?php

declare(strict_types=1);

namespace Honed\Action\Concerns;

use Honed\Action\ActionGroup;
use Honed\Action\Attributes\UseActions;
use ReflectionClass;

/**
 * @template TActionGroup of \Honed\Action\ActionGroup = \Honed\Action\ActionGroup
 *
 * @property class-string<TActionGroup> $actions
 */
trait HasActions
{
    /**
     * Get the action group instance for the model.
     *
     * @return TActionGroup
     */
    public static function actions()
    {
        return static::newActions()
            ?? ActionGroup::actionGroupForModel(static::class);
    }

    /**
     * Create a new action group instance for the model.
     *
     * @return TActionGroup
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
     * Get the actions from the Actions class attribute.
     *
     * @return class-string<TActionGroup>|null
     */
    protected static function getUseActionsAttribute()
    {
        $attributes = (new ReflectionClass(static::class))
            ->getAttributes(UseActions::class);

        if ($attributes !== []) {
            $group = $attributes[0]->newInstance();

            return $group->actionGroupClass;
        }

        return null;
    }
}
