<?php

declare(strict_types=1);

namespace Honed\Action\Http\Controllers;

use Honed\Action\ActionGroup;
use Illuminate\Routing\Controller;
use Honed\Action\Http\Requests\InvokableRequest;
use Honed\Action\Http\Requests\DispatchableRequest;

class ActionController extends Controller
{
    /**
     * Find and execute the appropriate action from route binding.
     *
     * @return \Illuminate\Contracts\Support\Responsable|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function invoke(InvokableRequest $request, ActionGroup $action)
    {
        return $action->handle($request);
    }

    /**
     * Find and execute the appropriate action from the request input.
     *
     * @return \Illuminate\Contracts\Support\Responsable|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function dispatch(DispatchableRequest $request)
    {
        /** @var string */
        $key = $request->validated('id');

        /** @var \Honed\Action\Contracts\Handles|null */
        $action = $this->baseClass()::makeFrom($key);

        abort_unless((bool) $action, 404);

        return $action->handle($request);
    }

    /**
     * Get the class to use to handle the actions.
     * 
     * @return class-string<\Honed\Action\Contracts\Handles>
     */
    public function baseClass()
    {
        return ActionGroup::class;
    }
}
