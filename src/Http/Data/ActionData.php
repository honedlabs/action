<?php

declare(strict_types=1);

namespace Honed\Action\Http\Data;

use Honed\Core\Contracts\TransferObject;

class ActionData implements TransferObject
{
    public function __construct(public readonly string $name) 
    {
        //
    }

    /**
     * Create a new data transfer object from a request.
     *
     * @param  \Honed\Action\Http\Requests\ActionRequest  $request
     */
    public static function from($request): static
    {
        return resolve(static::class, $request->validated());
    }
}
