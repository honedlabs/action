<?php

declare(strict_types=1);

namespace Honed\Action\Contracts;

/**
 * @phpstan-require-extends \Honed\Action\Action
 */
interface ShouldConfirm
{
    /**
     * Get the confirmation instance.
     *
     * @return \Honed\Action\Confirm
     */
    public function confirm();
}
