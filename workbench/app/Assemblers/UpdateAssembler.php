<?php

declare(strict_types=1);

namespace Workbench\App\Assemblers;

use Honed\Action\Assembler;
use Honed\Action\Operations\Operation;

class UpdateAssembler extends Assembler
{
    /**
     * Assemble an operation for serialization.
     */
    protected function definition(Operation $operation): Operation
    {
        return $operation
            ->name('update')
            ->icon('heroicon-o-pencil')
            ->label('Update');
    }
}
