<?php

declare(strict_types=1);

namespace Honed\Action\Tests\Stubs;

use Honed\Action\BulkAction;
use Honed\Action\Concerns\HasActions;
use Honed\Action\Contracts\DefinesActions;
use Honed\Action\InlineAction;
use Honed\Action\PageAction;

class Actions implements DefinesActions
{
    use HasActions;

    public function actions(): array
    {
        return [
            InlineAction::make('edit.product'),
            BulkAction::make('delete.products'),

            BulkAction::make('restore.products')->allow(false),
            PageAction::make('create.product'),

            PageAction::make('show.product')->allow(false),

        ];
    }
}
