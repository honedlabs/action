<?php

declare(strict_types=1);

namespace Honed\Action\Contracts;

use Illuminate\Database\Eloquent\Model;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 */
interface FromModel
{
    /**
     * Get the model class to use.
     *
     * @return class-string<TModel>
     */
    public function from(): string;
}
