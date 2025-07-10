<?php

declare(strict_types=1);

namespace Honed\Action\Contracts;

interface Relatable
{
    /**
     * Get the name of the relationship to use.
     */
    public function relationship(): string;
}
