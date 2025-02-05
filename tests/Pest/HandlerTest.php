<?php

declare(strict_types=1);

use Honed\Action\ActionHandler;
use Honed\Action\Concerns\HasActions;
use Honed\Action\Contracts\DefinesActions;
use Honed\Action\Tests\Stubs\Product;

class Actions implements DefinesActions
{
    use HasActions;

    public function actions(): array
    {
        return [

        ];
    }
}

beforeEach(function () {
    $this->test = new ActionHandler(
        new Actions,
        Product::query(),
        'public_id',
        true
    );
});