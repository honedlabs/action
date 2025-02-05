<?php

declare(strict_types=1);

namespace Honed\Action\Http\Data;

use Honed\Core\Contracts\TransferObject;

class BulkData implements TransferObject
{
    /**
     * @param  array<int,string|int>  $only
     * @param  array<int,string|int>  $except
     */
    public function __construct(
        public readonly string $name,
        public readonly array $only,
        public readonly array $except,
        public readonly bool $all,
    ) { }

    /**
     * Create a new bulk data transfer object.
     *
     * @param  \Honed\Action\Http\Requests\ActionRequest  $request
     */
    public static function from($request): static
    {
        return resolve(BulkData::class, [
            'name' => $request->input('name'),
            'except' => $request->input('except'),
            'only' => $request->input('only'),
            'all' => $request->input('all'),
        ]);
    }
}
