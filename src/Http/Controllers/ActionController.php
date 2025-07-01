<?php

declare(strict_types=1);

namespace Honed\Action\Http\Controllers;

use Honed\Action\Operations\Operation;
use Honed\Action\Unit;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ActionController extends Controller
{
    /**
     * Find and execute the appropriate action from route binding.
     *
     * @return \Illuminate\Contracts\Support\Responsable|\Symfony\Component\HttpFoundation\Response
     */
    public function __invoke(Request $request, Unit $unit, Operation $operation)
    {
        return $unit->handle($operation, $request);
    }
}
