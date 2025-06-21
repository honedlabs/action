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

arch('attributes')
    ->expect('Honed\Action\Attributes')
    ->toBeClasses();

arch('concerns')
    ->expect('Honed\Action\Concerns')
    ->toBeTraits();

arch('contracts')
    ->expect('Honed\Action\Contracts')
    ->toBeInterfaces();

arch('commands')
    ->expect('Honed\Action\Commands')
    ->toBeClasses()
    ->except('Honed\Action\Commands\Concerns\SuggestsModels')
    ->toExtend(Command::class);

arch('exceptions')
    ->expect('Honed\Action\Exceptions')
    ->toExtend(Exception::class);
