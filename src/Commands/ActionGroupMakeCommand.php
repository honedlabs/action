<?php

declare(strict_types=1);

namespace Honed\Action\Commands;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;

use function sprintf;
use function trim;

#[AsCommand(name: 'make:action-group')]
class ActionGroupMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:action-group';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new action group class.';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Action Group';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return $this->resolveStubPath('/stubs/honed.action-group.stub');
    }

    /**
     * Resolve the fully-qualified path to the stub.
     *
     * @param  string  $stub
     * @return string
     */
    protected function resolveStubPath($stub)
    {
        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
            ? $customPath
            : __DIR__.'/../..'.$stub;
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\ActionGroups';
    }

    /**
     * Get the console command options
     *
     * @return array<int,array<int,mixed>>
     */
    protected function getOptions()
    {
        return [
            [
                'force',
                null,
                InputOption::VALUE_NONE,
                sprintf(
                    'Create the class even if the %s already exists',
                    \mb_strtolower($this->type)
                ),
            ],
        ];
    }

    /**
     * Prompt for missing input arguments using the returned questions.
     *
     * @return array<string,mixed>
     */
    protected function promptForMissingArgumentsUsing()
    {
        return [
            'name' => [
                'What should the '.mb_strtolower($this->type).' be named?',
                'E.g. UserActions',
            ],
        ];
    }
}
