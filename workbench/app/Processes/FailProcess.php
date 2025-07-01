<?php

declare(strict_types=1);

namespace Workbench\App\Processes;

use Closure;
use Exception;

/**
 * @template TPayload
 * @template TResult
 *
 * @extends Honed\Command\Process<TPayload, TResult>
 */
class FailProcess extends ProductProcess
{
    /**
     * The tasks to be sequentially executed.
     *
     * @return array<int, class-string|Closure>
     */
    protected function tasks(): array
    {
        return [
            ...parent::tasks(),
            fn () => throw new Exception('Failed'),
        ];
    }
}
