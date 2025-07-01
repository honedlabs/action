<?php

declare(strict_types=1);

namespace Honed\Action\Operations;

use Honed\Action\Operations\Concerns\CanBeChunked;

class PageOperation extends Operation
{
    use CanBeChunked;
}
