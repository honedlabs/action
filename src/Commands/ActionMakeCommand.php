<?php

declare(strict_types=1);

namespace Honed\Action\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Database\Console\Factories\FactoryMakeCommand;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use function count;
use function Laravel\Prompts\select;
use function Laravel\Prompts\suggest;
use function trim;

#[AsCommand(name: 'make:action')]
class ActionMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:action';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new action class.';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Action';

    /**
     * Get the stub file for the generator.
     */
    protected function getStub(): string
    {
        $path = match (true) {
            (bool) $this->option('delete') => '/stubs/honed.action.delete.stub',
            (bool) $this->option('store') => '/stubs/honed.action.store.stub',
            (bool) $this->option('update') => '/stubs/honed.action.update.stub',
            default => '/stubs/honed.action.stub',
        };

        return $this->resolveStubPath($path);
    }

    /**
     * Resolve the fully-qualified path to the stub.
     *
     * @param  string  $stub
     */
    protected function resolveStubPath($stub): string
    {
        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
            ? $customPath
            : __DIR__.'/../..'.$stub;
    }

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     */
    protected function buildClass($name): string
    {
        $factory = class_basename(Str::ucfirst(str_replace('Factory', '', $name)));

        $namespaceModel = $this->option('model')
            ? $this->qualifyModel($this->option('model'))
            : $this->qualifyModel($this->guessModelName($name));

        $model = class_basename($namespaceModel);

        $namespace = $this->getNamespace(
            Str::replaceFirst($this->rootNamespace(), 'Database\\Factories\\', $this->qualifyClass($this->getNameInput()))
        );

        $replace = [
            '{{ factoryNamespace }}' => $namespace,
            'NamespacedDummyModel' => $namespaceModel,
            '{{ namespacedModel }}' => $namespaceModel,
            '{{namespacedModel}}' => $namespaceModel,
            'DummyModel' => $model,
            '{{ model }}' => $model,
            '{{model}}' => $model,
            '{{ factory }}' => $factory,
            '{{factory}}' => $factory,
        ];

        return str_replace(
            array_keys($replace), array_values($replace), parent::buildClass($name)
        );
    }

    /**
     * Guess the model name from the Factory name or return a default model name.
     */
    protected function guessModelName(string $name): string
    {
        if (str_ends_with($name, 'Action')) {
            $name = substr($name, 0, -6);
        }

        $name = match (true) {
            str_starts_with($name, 'Delete') => substr($name, 6),
            str_starts_with($name, 'Store') => substr($name, 5),
            str_starts_with($name, 'Update') => substr($name, 5),
            default => $name,
        };

        $modelName = $this->qualifyModel(Str::after($name, $this->rootNamespace()));

        if (class_exists($modelName)) {
            return $modelName;
        }

        if (is_dir(app_path('Models/'))) {
            return $this->rootNamespace().'Models\Model';
        }

        return $this->rootNamespace().'Model';
    }


    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Actions';
    }

    /**
     * Get the console command options.
     *
     * @return array<int, array<int, mixed>>
     */
    protected function getOptions()
    {
        return [
            ['force', null, InputOption::VALUE_NONE, 'Create the class even if the action already exists.'],
            ['model', 'm', InputOption::VALUE_REQUIRED, 'The model that the action is for.'],
            ['delete', 'd', InputOption::VALUE_NONE, 'Create a delete action.'],
            ['store', 's', InputOption::VALUE_NONE, 'Create a store action.'],
            ['update', 'u', InputOption::VALUE_NONE, 'Create an update action.'],
        ];
    }

        /**
     * Prompt for missing input arguments using the returned questions.
     *
     * @return array<string,mixed>
     */
    protected function promptForMissingArgumentsUsing(): array
    {
        return [
            'name' => [
                'What should the '.strtolower($this->type).' be named?',
                'E.g. StoreUserAction',
            ],
        ];
    }
}
