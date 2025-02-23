<?php

declare(strict_types=1);

namespace Honed\Action;

use Honed\Action\Concerns\HasActions;
use Honed\Core\Primitive;

/**
 * @extends \Honed\Core\Primitive<string,mixed>
 */
class ActionGroup extends Primitive
{
    use HasActions;

    /**
     * Create a new action group instance.
     *
     * @param  iterable<\Honed\Action\Action>  $actions
     */
    public static function make(iterable $actions = []): static
    {
        return resolve(static::class)
            ->addActions($actions);
    }

    /**
     * Set a single resource to apply the inline actions to.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $resource
     * @return $this
     */
    public function for($resource): static
    {
        // Builder or query
        return $this;
    }

    /**
     * Set multiple resources to apply the inline actions to each.
     *
     * @param  iterable<\Illuminate\Database\Eloquent\Model>  $resources
     * @return $this
     */
    public function forEach(iterable $resources): static
    {
        // Builder or query
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return [
            'actions' => $this->getActions(),
        ];
    }

    /**
     * @param  string  $name
     * @param  array<int, mixed>  $arguments
     * @return $this
     */
    public function _call($name, $arguments)
    {
        /** @var array<int, \Honed\Action\Action> $arguments */
        if ($name === 'actions') {
            return $this->addActions($arguments);
        }

        return $this;
    }
}
