<?php

declare(strict_types=1);

namespace Workbench\App\Actions\User;

use Honed\Action\Contracts\Action;

class DestroyUser implements Action
{
    /**
     * Handle the action.
     *
     * @param  \Workbench\App\Models\User  $record
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handle($record)
    {
        $record->delete();

        return back();
    }
}
