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

    /**
     * {@inheritdoc}
     *
     * @param  array<int, mixed>  $parameters
     */
    public function __call($method, $parameters)
    {
        if ($method === 'actions') {
            /** @var array<int, \Honed\Action\PageAction> $args */
            $args = $parameters[0] ?? [];

            return $this->withActions($args);
        }

        return parent::__call($method, $parameters);
    }
}
