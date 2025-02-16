<?php

declare(strict_types=1);

namespace Honed\Action\Tests\Pest\Fixtures;

use Honed\Action\BulkAction;
use Honed\Action\Concerns\HasActions;
use Honed\Action\InlineAction;
use Honed\Action\PageAction;

class Filled
{
    use HasActions;

    public function actions()
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
