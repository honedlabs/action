<?php

declare(strict_types=1);

namespace Honed\Action\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Finder\Finder;

use function Laravel\Prompts\error;
use function Laravel\Prompts\select;

#[AsCommand(name: 'make:actions')]
class ActionsMakeCommand extends Command implements PromptsForMissingInput
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:actions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a set of actions for a model.';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Actions';

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
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        /** @var string */
        $model = $this->argument('model');

        if (! \in_array($model, $this->possibleModels())) {
            error('The model '.$model.' does not exist.');

            return 1;
        }

        /** @var string|null */
        $path = $this->option('path');

        foreach ($this->actions as $action => $verb) {
            /** @var string */
            $model = $this->argument('model');

            $name = Str::of($path ?? '')
                ->append('/'.$model)
                ->append('/'.$verb.$model)
                ->replace('\\', '/')
                ->replace('//', '/')
                ->trim('/')
                ->value();

            $this->call('make:action', [
                'name' => $name,
                '--model' => $this->argument('model'),
                '--action' => $action,
            ]);
        }

        return 0;
    }

    /**
     * Get the console command arguments.
     *
     * @return array<int, array<int, mixed>>
     */
    protected function getArguments()
    {
        return [
            ['model', InputArgument::REQUIRED, 'The model for the '.\strtolower($this->type)],
        ];
    }

    /**
     * Prompt for missing input arguments using the returned questions.
     *
     * @return array<string, mixed>
     */
    protected function promptForMissingArgumentsUsing()
    {
        return [
            'model' => fn () => select(
                'What model should the '.strtolower($this->type).' be for?',
                $this->possibleModels()
            ),
        ];
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
            ['path', 'p', InputOption::VALUE_REQUIRED, 'Add an additional path to the actions directory.'],
        ];
    }

    /**
     * Get a list of possible model names.
     *
     * @return array<int, string>
     */
    protected function possibleModels()
    {
        $modelPath = is_dir(app_path('Models')) ? app_path('Models') : app_path();

        return (new Collection(Finder::create()->files()->depth(0)->in($modelPath)))
            ->map(fn ($file) => $file->getBasename('.php'))
            ->sort()
            ->values()
            ->all();
    }
}
