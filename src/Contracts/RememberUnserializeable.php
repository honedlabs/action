<?php

declare(strict_types=1);

namespace Honed\Action\Contracts;

interface RememberUnserializeable
{
    /**
     * Unserialize the data from a request parameter.
     */
    public function rememberUnserialize(int|string $key): self;
}
