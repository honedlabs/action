<?php

declare(strict_types=1);

namespace Honed\Action\Http\Data;

use Honed\Core\Contracts\Transferable;

class ActionData implements Transferable
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
