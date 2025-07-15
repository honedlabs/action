<?php

declare(strict_types=1);

namespace Honed\Action\Contracts;

use Illuminate\Contracts\Database\Eloquent\Builder;

interface FromEloquent
{
    /**
     * Get the eloquent resource to use as the source.
     *
     * @return class-string<\Illuminate\Database\Eloquent\Model>|\Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>|\Illuminate\Database\Eloquent\Relations\Relation<*, *, *>
     */
    public function from(): string|Builder;
}
