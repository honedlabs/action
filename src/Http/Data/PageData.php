<?php

declare(strict_types=1);

namespace Honed\Action\Http\Data;

use Honed\Core\Contracts\TransferObject;

class PageData implements TransferObject
{
    public function __construct(
        public readonly string $name,
    ) { }

    /**
     * Create a new inline data transfer object.
     *
     * @param  \Honed\Action\Http\Requests\ActionRequest  $request
     */
    public static function from($request): static
    {
        return resolve(PageData::class, [
            'name' => $request->input('name'),
        ]);
    }
}
