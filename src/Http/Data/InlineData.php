<?php

declare(strict_types=1);

namespace Honed\Action\Http\Data;

use Honed\Core\Contracts\TransferObject;

class InlineData extends ActionData
{
    public function __construct(
        public readonly string $name,
        public readonly int|string $id,
    ) { }

    /**
     * Create a new inline data transfer object.
     *
     * @param  \Honed\Action\Http\Requests\ActionRequest  $request
     */
    public static function from($request): static
    {
        return resolve(static::class, [
            'name' => $request->input('name'),
            'id' => $request->input('id'),
        ]);
    }
}
