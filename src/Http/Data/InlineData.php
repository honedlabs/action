<?php

declare(strict_types=1);

namespace Honed\Action\Http\Data;

class InlineData extends ActionData
{
    public function __construct(
        public readonly string $name,
        public readonly int|string $id,
    ) {
        //
    }
}
