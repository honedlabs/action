<?php

declare(strict_types=1);

namespace Honed\Action\Contracts;

/**
 * @phpstan-require-extends \Honed\Action\Action
 */
interface HasHandler
{
    /**
     * Set the action's handler.
     *
     * @template TModel of \Illuminate\Database\Eloquent\Model
     * 
     * @param  TModel|\Illuminate\Database\Eloquent\Builder<TModel>  $data
     * @return \Illuminate\Contracts\Support\Responsable|\Illuminate\Http\RedirectResponse|null
     */
    public function handle($data);
}
