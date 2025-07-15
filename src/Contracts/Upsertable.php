<?php

declare(strict_types=1);

namespace Honed\Action\Contracts;

interface Upsertable extends FromEloquent
{
    /**
     * Get the unique by columns for the upsert.
     *
     * @return array<int, string>
     */
    public function uniqueBy(): array;

    /**
     * Get the columns to update in the upsert.
     *
     * @return array<int, string>
     */
    public function update(): array;
}
