<?php

declare(strict_types=1);

namespace Honed\Action\Contracts;

interface Executes
{
    /**
     * Execute the action handler using the provided data.
     *
     * @param  \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>  $parameter
     * @return mixed
     */
    public function execute($parameter);
}
