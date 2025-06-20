<?php

declare(strict_types=1);

namespace Honed\Action\Concerns;

use Closure;
use Honed\Action\Confirm;

use function is_null;

/**
 * @phpstan-require-extends \Honed\Core\Primitive
 */
trait HasConfirm
{
    /**
     * The instance of the confirm.
     *
     * @var Confirm|null
     */
    protected $confirm;

    /**
     * Set the confirm for the instance.
     *
     * @param  Confirm|Closure|string|null  $confirm
     * @param  string|null  $description
     * @return $this
     */
    public function confirm($confirm, $description = null)
    {
        match (true) {
            is_null($confirm) => null,
            $confirm instanceof Confirm => $this->confirm = $confirm,
            $confirm instanceof Closure => $this->evaluate($confirm),
            default => $this->newConfirm()
                ->title($confirm)
                ->description($description)
        };

        return $this;
    }

    /**
     * Retrieve the confirm for the instance.
     *
     * @return Confirm|null
     */
    public function getConfirm()
    {
        return $this->confirm;
    }

    /**
     * Access the confirm for this instance.
     *
     * @return Confirm
     */
    protected function newConfirm()
    {
        return $this->confirm ??= Confirm::make();
    }
}
