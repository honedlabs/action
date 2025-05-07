<?php

declare(strict_types=1);

namespace Honed\Action\Http\Controllers;

use Honed\Action\ActionGroup;
use Honed\Action\Exceptions\CouldNotResolveHandlerException;
use Honed\Action\Http\Requests\DispatchableRequest;
use Honed\Action\Http\Requests\InvokableRequest;
use Illuminate\Routing\Controller;

class ActionController extends Controller
{
    /**
     * Find and execute the appropriate action from route binding.
     *
     * @template TModel of \Illuminate\Database\Eloquent\Model
     * @template TBuilder of \Illuminate\Database\Eloquent\Builder<TModel>
     *
     * @param  \Honed\Action\ActionGroup<TModel, TBuilder>  $action
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
     * @throws \Honed\Action\Exceptions\CouldNotResolveHandlerException
     * @throws \Honed\Action\Exceptions\ActionNotFoundException
     * @throws \Honed\Action\Exceptions\ActionNotAllowedException
     * @throws \Honed\Action\Exceptions\InvalidActionException
     */
    public function dispatch(DispatchableRequest $request)
    {
        /** @var string */
        $key = $request->validated('id');

        /** @var \Honed\Action\Contracts\Handles|null */
        $action = $this->baseClass()::tryFrom($key);

        if (! $action) {
            CouldNotResolveHandlerException::throw();
        }

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
