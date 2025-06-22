<?php

declare(strict_types=1);

namespace Honed\Action\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
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
class ActionMakeCommand extends GeneratorCommand implements PromptsForMissingInput
{
    use Concerns\SuggestsModels;

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
    protected $description = 'Create a new actionable class.';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Action';

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildClass($name)
    {
        $replace = $this->buildModelReplacements();

        $action = $this->option('action');

        if ($action && ! in_array($action, array_keys($this->getActions()))) {
            throw new InvalidArgumentException('You have supplied an invalid action.');
        }

        return str_replace(
            array_keys($replace), array_values($replace), parent::buildClass($name)
        );
    }

    /**
     * Build the model replacement values.
     *
     * @return array<string, string>
     */
    protected function buildModelReplacements()
    {
        /** @var string|null $model */
        $model = $this->option('model');

        $modelClass = $model ? $this->parseModel($model) : Model::class;

        $this->promptForModelCreation($modelClass);

        return [
            'DummyFullModelClass' => $modelClass,
            '{{ namespacedModel }}' => $modelClass,
            '{{namespacedModel}}' => $modelClass,
            'DummyModelClass' => class_basename($modelClass),
            '{{ model }}' => class_basename($modelClass),
            '{{model}}' => class_basename($modelClass),
            'DummyModelVariable' => lcfirst(class_basename($modelClass)),
            '{{ modelVariable }}' => lcfirst(class_basename($modelClass)),
            '{{modelVariable}}' => lcfirst(class_basename($modelClass)),
        ];
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        /** @var string|null */
        $action = $this->option('action');

        if (! $action) {
            return $this->resolveStubPath('/stubs/honed.action.stub');
        }

        return $this->resolveStubPath("/stubs/honed.action.{$action}.stub");
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
            ['action', 'a', InputOption::VALUE_REQUIRED, 'The action to be used.'],
            ['model', 'm', InputOption::VALUE_REQUIRED, 'The model that the action is for.'],
        ];
    }

    /**
     * Interact further with the user if they were prompted for missing arguments.
     *
     * @return void
     */
    protected function afterPromptingForMissingArguments(InputInterface $input, OutputInterface $output)
    {
        if ($this->isReservedName($this->getNameInput()) || $this->didReceiveOptions($input)) {
            return;
        }

        $actions = [...$this->getActions(), null => 'None'];

        $input->setOption('action', select(
            'What action should be used? (Optional)',
            $actions,
            scroll: count($actions),
            hint: 'If no action is provided, the default action stub will be used.',
        ));

        $input->setOption('model', suggest(
            'What model should this action be for? (Optional)',
            $this->possibleModels(),
            required: 'This field is required when an action is selected',
        ));
    }

    /**
     * Get the action to be used.
     *
     * @return array<string,string>
     */
    protected function getActions()
    {
        $actions = config('action.actions');

        if (is_array($actions) && Arr::isAssoc($actions)) {
            /** @var array<string,string> */
            return $actions;
        }

        return [
            'associate' => 'Associate',
            'attach' => 'Attach',
            'detach' => 'Detach',
            'destroy' => 'Destroy',
            'dispatch' => 'Dispatch',
            'dissociate' => 'Dissociate',
            'force-destroy' => 'Force Destroy',
            'replicate' => 'Replicate',
            'restore' => 'Restore',
            'store' => 'Store',
            'sync' => 'Sync',
            'toggle' => 'Toggle',
            'touch' => 'Touch',
            'update' => 'Update',
            'upsert' => 'Upsert',
        ];
    }
}
