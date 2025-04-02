<?php

declare(strict_types=1);

namespace Honed\Action\Http\Controllers;

use Honed\Action\Action;
use Honed\Action\Http\Requests\ActionRequest;
use Illuminate\Routing\Controller;

class ActionController extends Controller
{
    // public function __invoke(ActionRequest $request)
    // {
    //     /** @var string */
    //     $key = $request->validated('id');

    //     $action = Action::getPrimitive($key, Action::class);

    //     abort_unless((bool) $action, 404);

    //     return $action->handle($request);
    // }
}
