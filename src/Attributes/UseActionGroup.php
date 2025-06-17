<?php

declare(strict_types=1);

namespace Honed\Action\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class UseActionGroup
{
    /**
     * Create a new attribute instance.
     *
     * @param  class-string<\Honed\Action\ActionGroup>  $actionGroupClass
     * @return void
     */
    public function __construct(
        public string $actionGroupClass
    ) {}
}
