<?php

declare(strict_types=1);

namespace Honed\Action\Concerns;

use Honed\Action\Contracts\Action;

/**
 * @template TAction of \Honed\Action\Contracts\Action
 *
 * @phpstan-require-implements \Honed\Action\Contracts\Action
 */
trait ForwardsActions
{
    /**
     * The action instance.
     *
     * @var TAction
     */
    protected $action;

    /**
     * Get the action class to forward to.
     *
     * @return class-string<TAction>
     */
    abstract public function action(): string;

    /**
     * Get the action to forward to.
     *
     * @return TAction
     */
    public function getAction(): Action
    {
        return $this->action ??= resolve($this->action());
    }

    /**
     * Forward the action.
     */
    protected function forward(mixed ...$arguments): mixed
    {
        return $this->getAction()->handle(...$arguments);
    }
}
