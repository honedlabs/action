<?php

declare(strict_types=1);

namespace Honed\Action\Http\Data;

use Honed\Action\Creator;
use Honed\Action\Http\Requests\ActionRequest;

final class ActionData
{
    /**
     * Create a new action data transfer object.
     *
     * @param  \Honed\Action\Http\Requests\ActionRequest  $request
     */
    public static function from($request): InlineData|BulkData|PageData
    {
        $type = $request->input('type');

        return match ($type) {
            Creator::Inline => InlineData::from($request),
            Creator::Bulk => BulkData::from($request),
            Creator::Page => PageData::from($request),
            default => throw new \InvalidArgumentException(\sprintf('The action type [%s] is not supported.', $type)),
        };
    }
}
