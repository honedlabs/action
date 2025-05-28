<?php

declare(strict_types=1);

namespace Honed\Action\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class ActionGroup
{
    /**
     * Create a new attribute instance.
     *
     * @param  class-string<\Honed\Action\ActionGroup>  $group
     * @return void
     */
    public function __construct(
        public string $group
    ) {}

    /**
     * Get the action group.
     *
     * @return class-string<\Honed\Action\ActionGroup>
     */
    public function getGroup()
    {
        return $this->group;
    }
}
