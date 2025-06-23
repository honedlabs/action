<?php

declare(strict_types=1);

namespace Honed\Action\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Support\Arr;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

use function mb_strtolower;

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
        /** @var string|null $model */
        $model = $this->argument('model');

        if ($model) {
            $this->promptForModelCreation($model);
        }

        foreach ($this->getActions() as $action => $verb) {
            $path = $this->getClassPath($model ?? '', $verb);

            $this->call('make:action', array_filter([
                'name' => $path,
                '--model' => $model ? class_basename($model) : null,
                '--action' => $action,
                '--force' => (bool) $this->option('force'),
            ]));
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
            ['model', InputArgument::OPTIONAL, 'The model for the '.mb_strtolower($this->type)],
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
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return '';
    }

    /**
     * Get the path for the action.
     *
     * @param  string  $model
     * @param  string  $action
     * @return string
     */
    protected function getClassPath($model, $action)
    {
        $name = class_basename($model).$action;

        /** @var string|null $path */
        $path = $this->option('path');

        if ($path) {
            return trim($path, ' /').'/'.$name;
        }

        return $name;
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
