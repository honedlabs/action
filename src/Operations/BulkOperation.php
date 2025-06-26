<?php

declare(strict_types=1);

namespace Honed\Action\Operations;

class BulkOperation extends Operation
{
    use Concerns\CanBeChunked;
    use Concerns\CanKeepSelected;

    /**
     * Provide the instance with any necessary setup.
     *
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();

        $this->type(self::BULK);
    }

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
