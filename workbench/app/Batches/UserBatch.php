<?php

declare(strict_types=1);

namespace Workbench\App\Batches;

use Honed\Action\Batch;
use Honed\Action\Operations\BulkOperation;
use Honed\Action\Operations\InlineOperation;
use Honed\Action\Operations\PageOperation;
use Workbench\App\Models\User;

/**
 * @template TModel of \Workbench\App\Models\User = \Workbench\App\Models\User
 * @template TBuilder of \Illuminate\Database\Eloquent\Builder<TModel> = \Illuminate\Database\Eloquent\Builder<TModel>
 *
 * @extends \Honed\Action\Batch<TModel, TBuilder>
 */
class UserBatch extends Batch
{
    protected function definition(Batch $actions): Batch
    {
        return $actions
            ->actions([
                InlineOperation::make('show')
                    ->route(fn ($user) => route('users.show', $user)),

                InlineOperation::make('update.name')
                    ->action(fn ($user) => $user->update(['name' => 'test'])),

                InlineOperation::make('update.description')
                    ->action(fn ($user) => $user->update(['name' => 'description']))
                    ->allow(false),

                BulkOperation::make('update.name')
                    ->action(fn ($user) => $user->update(['name' => 'test']))
                    ->allow(false),

                BulkOperation::make('update.description')
                    ->action(fn ($user) => $user->update(['name' => 'description'])),

                PageOperation::make('create')
                    ->route('users.create'),

                PageOperation::make('create.name')
                    ->action(fn () => User::factory()->create([
                        'name' => 'name',
                    ])),

                PageOperation::make('create.description')
                    ->action(fn () => User::factory()->create([
                        'name' => 'description',
                    ]))
                    ->allow(false),
            ]);
    }
}
