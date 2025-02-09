<?php

declare(strict_types=1);

namespace Honed\Action\Http\Data;

class BulkData extends ActionData
{
    /**
     * @param  array<int,string|int>  $only
     * @param  array<int,string|int>  $except
     */
    public function __construct(
        public readonly string $name,
        public readonly array $only,
        public readonly array $except,
        public readonly bool $all,
    ) {
        //
    }
}
