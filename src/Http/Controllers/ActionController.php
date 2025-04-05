<?php

declare(strict_types=1);

namespace Honed\Action\Http\Controllers;

use Honed\Action\Action;
use Honed\Action\Http\Requests\ActionRequest;
use Illuminate\Routing\Controller;

class ActionController extends Controller
{
    // /**
    //  * Delegate the incoming action request to the appropriate table.
    //  *
    //  * @return \Illuminate\Contracts\Support\Responsable|\Symfony\Component\HttpFoundation\RedirectResponse|void
    //  */
    // public function __invoke(ActionRequest $request)
    // {
    //     /** @var string */
    //     $key = $request->validated('id');

    //     $action = Action::getPrimitive($key, Action::class);

    //     abort_unless((bool) $action, 404);

    //     return $action->handle($request);
    // }
}
