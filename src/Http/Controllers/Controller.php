<?php

declare(strict_types=1);

namespace Honed\Action\Http\Controllers;

use Honed\Action\ActionGroup;
use Honed\Action\Exceptions\CouldNotResolveHandlerException;
use Honed\Action\Http\Requests\DispatchableRequest;
use Honed\Action\Http\Requests\InvokableRequest;
use Illuminate\Routing\Controller as BaseController;

abstract class Controller extends BaseController
{
    /**
     * Get the class containing the action handler.
     *
     * @return class-string<\Honed\Action\Contracts\HandleActions>
     */
    abstract protected function from();

    /**
     * Find and execute the appropriate action from route binding.
     *
     * @return \Illuminate\Contracts\Support\Responsable|\Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Honed\Action\Exceptions\ActionNotFoundException
     * @throws \Honed\Action\Exceptions\ActionNotAllowedException
     * @throws \Honed\Action\Exceptions\InvalidActionException
     */
    public function invoke(InvokableRequest $request, ActionGroup $action)
    {
        return $action->handle($request);
    }

    /**
     * Find and execute the appropriate action from the request input.
     *
     * @return \Illuminate\Contracts\Support\Responsable|\Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws CouldNotResolveHandlerException
     * @throws \Honed\Action\Exceptions\ActionNotFoundException
     * @throws \Honed\Action\Exceptions\ActionNotAllowedException
     * @throws \Honed\Action\Exceptions\InvalidActionException
     */
    public function dispatch(DispatchableRequest $request)
    {
        /** @var string */
        $key = $request->validated('id');

        /** @var \Honed\Action\Contracts\HandlesActions|null */
        $action = $this->from()::find($key);

        if (! $action) {
            CouldNotResolveHandlerException::throw();
        }

        return $action->handle($request);
    }
}
