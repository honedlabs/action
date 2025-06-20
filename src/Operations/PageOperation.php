<?php

declare(strict_types=1);

namespace Honed\Action\Operations;

class PageOperation extends Operation
{
    use Concerns\CanBeChunked;

    /**
     * Provide the instance with any necessary setup.
     *
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();

        $this->type(self::PAGE);
    }
}
