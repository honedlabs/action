<?php

declare(strict_types=1);

namespace Honed\Action\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Remember
{
    public function __construct() {}
}
