<?php

declare(strict_types=1);

namespace Honed\Action\Tests\Fixtures;

use Honed\Action\ActionGroup;
use Honed\Action\PageAction;

class ProductActions extends ActionGroup
{
    /**
     * {@inheritdoc}
     */
    public function defineActions()
    {
        return [
            PageAction::make('create')
                ->action(fn () => product()),
        ];
    }
}
