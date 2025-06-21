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
     * Get the instance as an array.
     * 
     * @return array<string, mixed>
     */
    public function toArray()
    {
        return [
            ...parent::toArray(),
            'keep' => $this->keepsSelected(),
        ];
    }
}
