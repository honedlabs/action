<?php

declare(strict_types=1);

namespace Honed\Action\Concerns;

use Honed\Action\Action;
use Honed\Action\ActionGroup;
use Honed\Action\BulkAction;
use Honed\Action\InlineAction;
use Honed\Action\PageAction;
use Honed\Action\Support\Constants;
use Honed\Core\Parameters;
use Illuminate\Support\Arr;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model = \Illuminate\Database\Eloquent\Model
 * @template TBuilder of \Illuminate\Database\Eloquent\Builder<TModel> = \Illuminate\Database\Eloquent\Builder<TModel>
 *
 * @phpstan-require-extends \Honed\Core\Primitive
 */
trait HasActions
{
    /**
     * List of the actions.
     *
     * @var array<int,\Honed\Action\Action|\Honed\Action\ActionGroup<TModel, TBuilder>>
     */
    protected $actions = [];

    /**
     * Merge a set of actions with the existing.
     *
     * @param  \Honed\Action\Action|\Honed\Action\ActionGroup<TModel, TBuilder>|iterable<int, \Honed\Action\Action|\Honed\Action\ActionGroup<TModel, TBuilder>>  ...$actions
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
     * @return array<int,\Honed\Action\Action|\Honed\Action\ActionGroup<TModel, TBuilder>>
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
     * Determines if the instance has any actions.
     *
     * @return bool
     */
    public function hasActions()
    {
        return filled($this->getActions());
    }

    /**
     * Determine if the instance provides any actions.
     *
     * @return bool
     */
    public function isActionable()
    {
        return $this->hasAny(Constants::INLINE, Constants::BULK, Constants::PAGE);
    }

    /**
     * Determine if the instance does not provide any actions.
     *
     * @return bool
     */
    public function isNotActionable()
    {
        return ! $this->isActionable();
    }

    /**
     * Determine if the instance does not provide any actions.
     *
     * @return bool
     */
    public function isntActionable()
    {
        return $this->isNotActionable();
    }

    /**
     * Set the instance to not provide any actions.
     *
     * @return $this
     */
    public function exceptActions()
    {
        return $this->except(Constants::INLINE, Constants::BULK, Constants::PAGE);
    }

    /**
     * Set the instance to only provide actions.
     *
     * @return $this
     */
    public function onlyActions()
    {
        return $this->only(Constants::INLINE, Constants::BULK, Constants::PAGE);
    }

    /**
     * Set the instance to only provide inline actions.
     *
     * @return $this
     */
    public function onlyInlineActions()
    {
        return $this->only(Constants::INLINE);
    }

    /**
     * Set the instance to not provide inline actions.
     *
     * @return $this
     */
    public function exceptInlineActions()
    {
        return $this->except(Constants::INLINE);
    }

    /**
     * Determine if the instance provides inline actions.
     *
     * @return bool
     */
    public function providesInlineActions()
    {
        return $this->has(Constants::INLINE);
    }

    /**
     * Set the instance to only provide bulk actions.
     *
     * @return $this
     */
    public function onlyBulkActions()
    {
        return $this->only(Constants::BULK);
    }

    /**
     * Set the instance to not provide bulk actions.
     *
     * @return $this
     */
    public function exceptBulkActions()
    {
        return $this->except(Constants::BULK);
    }

    /**
     * Determine if the instance provides bulk actions.
     *
     * @return bool
     */
    public function providesBulkActions()
    {
        return $this->has(Constants::BULK);
    }

    /**
     * Set the instance to only provide page actions.
     *
     * @return $this
     */
    public function onlyPageActions()
    {
        return $this->only(Constants::PAGE);
    }

    /**
     * Set the instance to not provide page actions.
     *
     * @return $this
     */
    public function exceptPageActions()
    {
        return $this->except(Constants::PAGE);
    }

    /**
     * Determine if the instance provides page actions.
     *
     * @return bool
     */
    public function providesPageActions()
    {
        return $this->has(Constants::PAGE);
    }

    /**
     * Retrieve only the allowed inline actions.
     *
     * @return array<int,\Honed\Action\InlineAction>
     */
    public function getInlineActions()
    {
        if (! $this->providesInlineActions()) {
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
        if (! $this->providesBulkActions()) {
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
        if (! $this->providesPageActions()) {
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
