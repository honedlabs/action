<?php

declare(strict_types=1);

use Illuminate\Console\Command;

arch()->preset()->php();

arch()->preset()->security();

arch('it will not use debugging functions')
    ->expect(['dd', 'dump', 'ray'])
    ->each->not->toBeUsed();

arch('strict types')
    ->expect('Honed\Action')
    ->toUseStrictTypes();

arch('concerns')
    ->expect('Honed\Action\Concerns')
    ->toBeTraits();

arch('contracts')
    ->expect('Honed\Action\Contracts')
    ->toBeInterfaces();

arch('commands')
    ->expect('Honed\Action\Console\Commands')
    ->toBeClasses()
    ->toExtend(Command::class);
