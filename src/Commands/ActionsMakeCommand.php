<?php

declare(strict_types=1);

namespace Honed\Action\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

use function Laravel\Prompts\error;
use function Laravel\Prompts\select;

#[AsCommand(name: 'make:actions')]
class ActionsMakeCommand extends Command implements PromptsForMissingInput
{
    use Concerns\SuggestsModels;

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
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $model = $this->getModel($this->argument('model'));

        if (! $model) {
            return $this->missingModel();
        }

        /** @var string|null */
        $path = $this->option('path');

        $force = (bool) $this->option('force');

        foreach ($this->getActions() as $action => $verb) {
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
                '--force' => $force,
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
            ['model', InputArgument::REQUIRED, 'The model for the '.\mb_strtolower($this->type)],
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
                'What model should the '.mb_strtolower($this->type).' be for?',
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
     * Error the command when the model does not exist.
     *
     * @return int
     */
    protected function missingModel()
    {
        error('The model does not exist.');

        return 1;
    }

    /**
     * Get the actions that can be used in the action.
     *
     * @return array<string,string>
     */
    protected function getActions()
    {
        $actions = config('action.model_actions');

        if (is_array($actions) && Arr::isAssoc($actions)) {
            /** @var array<string,string> */
            return $actions;
        }

        return [
            'store' => 'Store',
            'update' => 'Update',
            'destroy' => 'Destroy',
        ];
    }
}
