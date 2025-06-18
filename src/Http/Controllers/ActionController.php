<?php

declare(strict_types=1);

namespace Honed\Action\Http\Controllers;

use Honed\Action\Batch;
use Honed\Action\Http\Requests\InvokableRequest;

class ActionController extends Controller
{
    /**
     * Find and execute the appropriate action from route binding.
     *
     * @template TModel of \Illuminate\Database\Eloquent\Model
     * @template TBuilder of \Illuminate\Database\Eloquent\Builder<TModel>
     *
     * @param  Batch<TModel, TBuilder>  $action
     * @return \Illuminate\Contracts\Support\Responsable|\Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Honed\Action\Exceptions\ActionNotFoundException
     * @throws \Honed\Action\Exceptions\ActionNotAllowedException
     * @throws \Honed\Action\Exceptions\InvalidActionException
     */
    public function invoke(InvokableRequest $request, Batch $action)
    {
        return $action->handle($request);
    }

    /**
     * Get the class containing the action handler.
     *
     * @return class-string<\Honed\Action\Contracts\HandlesActions>
     */
    protected function from()
    {
        return Batch::class;
    }
}
