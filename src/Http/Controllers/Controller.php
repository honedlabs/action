<?php

declare(strict_types=1);

namespace Honed\Action\Http\Controllers;

use Honed\Action\Exceptions\CouldNotResolveHandlerException;
use Honed\Action\Http\Requests\DispatchableRequest;
use Illuminate\Routing\Controller as BaseController;

abstract class Controller extends BaseController
{
    /**
     * Get the class containing the action handler.
     *
     * @return class-string<\Honed\Action\Contracts\HandlesOperations>
     */
    abstract protected function from();

    /**
     * Find and execute the appropriate action from the request input.
     *
     * @return \Illuminate\Contracts\Support\Responsable|\Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws CouldNotResolveHandlerException
     * @throws \Honed\Action\Exceptions\InvalidOperationException
     * @throws \Honed\Action\Exceptions\OperationNotFoundException
     */
    public function dispatch(DispatchableRequest $request)
    {
        /** @var string */
        $key = $request->validated('id');

        /** @var \Honed\Action\Contracts\HandlesOperations|null */
        $action = $this->from()::find($key);

        if (! $action) {
            CouldNotResolveHandlerException::throw();
        }

        return $action->handle($request);
    }
}
