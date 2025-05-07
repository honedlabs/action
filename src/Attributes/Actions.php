<?php

declare(strict_types=1);

namespace Honed\Action\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class Actions
{
    /**
     * Create a new attribute instance.
     *
     * @param  class-string<\Honed\Action\ActionGroup>  $actions
     * @return void
     */
    public function __construct(
        public string $actions
    ) {}
}
