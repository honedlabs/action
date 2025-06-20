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
     * @return \Illuminate\Contracts\Support\Responsable|\Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Honed\Action\Exceptions\InvalidOperationException
     * @throws \Honed\Action\Exceptions\OperationNotFoundException
     */
    public function invoke(InvokableRequest $request, Batch $action)
    {
        return $action->handle($request);
    }

    /**
     * Get the class containing the action handler.
     *
     * @return class-string<\Honed\Action\Contracts\HandlesOperations>
     */
    protected function from()
    {
        return Batch::class;
    }
}
