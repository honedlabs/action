<?php

declare(strict_types=1);

namespace Honed\Action\Commands\Concerns;

use Illuminate\Support\Collection;
use Symfony\Component\Finder\Finder;

use function in_array;

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
     * @param  string  $model
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
