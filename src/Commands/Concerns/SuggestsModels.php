<?php

declare(strict_types=1);

namespace Honed\Action\Commands\Concerns;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Finder\Finder;

use function in_array;
use function Laravel\Prompts\error;
use function Laravel\Prompts\select;

/**
 * @phpstan-require-extends \Illuminate\Console\Command
 */
trait SuggestsModels
{
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

    /**
     * Get the model for the action.
     * 
     * @param string $model
     * @return string|null
     */
    protected function getModel($model)
    {
        if (! in_array($model, $this->possibleModels())) {
            return null;
        }

        return $model;
    }
}