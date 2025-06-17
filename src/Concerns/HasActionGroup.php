<?php

declare(strict_types=1);

namespace Honed\Action\Concerns;

use Honed\Action\ActionGroup;
use Honed\Action\Attributes\UseActionGroup;
use ReflectionClass;

/**
 * @template TActionGroup of \Honed\Action\ActionGroup = \Honed\Action\ActionGroup
 *
 * @property class-string<TActionGroup> $actionGroup
 */
trait HasActionGroup
{
    /**
     * Get the action group instance for the model.
     *
     * @return TActionGroup
     */
    public static function actionGroup()
    {
        return static::newActionGroup()
            ?? ActionGroup::actionGroupForModel(static::class);
    }

    /**
     * Create a new action group instance for the model.
     *
     * @return TActionGroup
     */
    protected static function newActionGroup()
    {
        if (isset(static::$actionGroup)) {
            return static::$actionGroup::make();
        }

        if ($actionGroup = static::getActionGroupAttribute()) {
            return $actionGroup::make();
        }

        return null;
    }

    /**
     * Get the actions from the UseActionGroup class attribute.
     *
     * @return class-string<TActionGroup>|null
     */
    protected static function getUseActionGroupAttribute()
    {
        $attributes = (new ReflectionClass(static::class))
            ->getAttributes(UseActionGroup::class);

        if ($attributes !== []) {
            $group = $attributes[0]->newInstance();

            return $group->actionGroupClass;
        }

        return null;
    }
}
