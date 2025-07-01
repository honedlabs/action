<?php

declare(strict_types=1);

namespace Honed\Action\Operations;

use Honed\Action\Operations\Concerns\CanBeChunked;
use Honed\Action\Operations\Concerns\CanKeepSelected;

class BulkOperation extends Operation
{
    use CanBeChunked;
    use CanKeepSelected;

    /**
     * Get the representation of the instance.
     *
     * @return array<string, mixed>
     */
    protected function representation(): array
    {
        return [
            ...parent::representation(),
            'keep' => $this->keepsSelected(),
        ];
    }
}
