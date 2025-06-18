<?php

declare(strict_types=1);

namespace Honed\Action\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use function array_merge;
use function count;
use function Laravel\Prompts\error;
use function Laravel\Prompts\select;
use function Laravel\Prompts\suggest;

#[AsCommand(name: 'make:action')]
class ActionMakeCommand extends GeneratorCommand implements PromptsForMissingInput
{
    /**
     * The actions that can be used in the action.
     *
     * @var array<string,string>
     */
    public $actions = [
        'index' => 'Index',
        'create' => 'Create',
        'store' => 'Store',
        'show' => 'Show',
        'edit' => 'Edit',
        'update' => 'Update',
        'delete' => 'Delete',
        'destroy' => 'Destroy',
    ];

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
        $stub = parent::buildClass($name);

        /** @var string|null */
        $model = $this->option('model');

        /** @var string|null */
        $action = $this->option('action');

        if ($action && ! $model) {
            error('You must provide a model when specifying the action type.');
        }

        return $model
            ? $this->replaceModel($stub, $model)
            : $stub;
    }

    /**
     * Replace the model for the given stub.
     *
     * @param  string  $stub
     * @param  string  $model
     * @return string
     */
    protected function replaceModel($stub, $model)
    {
        $model = str_replace('/', '\\', $model);

        if (str_starts_with($model, '\\')) {
            $namespacedModel = trim($model, '\\');
        } else {
            $namespacedModel = $this->qualifyModel($model);
        }

        $model = class_basename(trim($model, '\\'));

        $dummyModel = Str::camel($model) === 'user' ? 'model' : $model;

        $replace = [
            'NamespacedDummyModel' => $namespacedModel,
            '{{ namespacedModel }}' => $namespacedModel,
            '{{namespacedModel}}' => $namespacedModel,
            'DummyModel' => $model,
            '{{ model }}' => $model,
            '{{model}}' => $model,
            'dummyModel' => Str::camel($dummyModel),
            '{{ modelVariable }}' => Str::camel($dummyModel),
            '{{modelVariable}}' => Str::camel($dummyModel),
        ];

        $stub = str_replace(
            array_keys($replace), array_values($replace), $stub
        );

        $contract = "use Honed\Action\Contracts\Action;\r\n";

        /** @var string */
        return preg_replace(
            '/'.preg_quote($contract, '/').'/',
            vsprintf("use %s;\r\nuse %s;\r\n", [
                'Honed\Action\Contracts\Action',
                $namespacedModel,
            ]),
            $stub
        );
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

        $stub = Str::of($action)
            ->lower()
            ->prepend('/stubs/honed.action.')
            ->append('.stub')
            ->value();

        return $this->resolveStubPath($stub);
    }

    /**
     * Resolve the fully-qualified path to the stub.
     *
     * @param  string  $stub
     * @return string
     */
    protected function resolveStubPath($stub)
    {
        return file_exists($customPath = $this->laravel->basePath(\trim($stub, '/')))
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
            ['model', 'm', InputOption::VALUE_REQUIRED, 'The model that the action is for.'],
            ['action', 'a', InputOption::VALUE_REQUIRED, 'The action to be used.'],
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

        $actions = array_merge($this->actions, [
            'none' => 'None',
        ]);

        $action = select(
            'What action should be used? (Optional)',
            $actions,
            scroll: count($actions),
            hint: 'If no action is provided, the default action stub will be used.',
        );

        if ($action === 'none') {
            return;
        }

        // If a action is selected, they must also provide a model.
        $input->setOption('action', $action);

        $model = suggest(
            'What model should this action be for? (Optional)',
            $this->possibleModels(),
            required: 'This field is required when an action is selected',
        );

        $input->setOption('model', $model);
    }
}
