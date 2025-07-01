<?php

declare(strict_types=1);

namespace Honed\Action\Commands;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;

use function trim;

#[AsCommand(name: 'make:operation')]
class OperationMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:operation';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $description = 'Create a new action operation class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Operation';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return $this->resolveStubPath($this->getOperationStub());
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getOperationStub()
    {
        return match (true) {
            (bool) $this->option('inline'), $this->option('type') === 'inline' => '/stubs/honed.operation.inline.stub',
            (bool) $this->option('bulk'), $this->option('type') === 'bulk' => '/stubs/honed.operation.bulk.stub',
            (bool) $this->option('page'), $this->option('type') === 'page' => '/stubs/honed.operation.page.stub',
            default => '/stubs/honed.operation.inline.stub',
        };
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
        return $rootNamespace.'\Operations';
    }

    /**
     * @return array<int,array<int,mixed>>
     */
    protected function getOptions(): array
    {
        return [
            ['force', 'f', InputOption::VALUE_NONE, 'Create the class even if the operation already exists'],
            ['type', 't', InputOption::VALUE_OPTIONAL, 'The type of operation to create'],
            ['inline', 'i', InputOption::VALUE_NONE, 'Create an inline operation'],
            ['bulk', 'b', InputOption::VALUE_NONE, 'Create a bulk operation'],
            ['page', 'p', InputOption::VALUE_NONE, 'Create a page operation'],
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
                'E.g. View',
            ],
        ];
    }
}
