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
    /**
     * Define the operations for the batch.
     *
     * @param  $this  $batch
     * @return $this
     */
    protected function definition(Batch $batch): Batch
    {
        return $batch
            ->for(User::class)
            ->operations([
                InlineOperation::make('show')
                    ->route(fn ($record) => route('users.show', $record)),

                InlineOperation::make('update.name')
                    ->action(fn ($record) => $record->update(['name' => 'test'])),

                InlineOperation::make('update.description')
                    ->action(fn ($record) => $record->update(['name' => 'description']))
                    ->allow(false),

                BulkOperation::make('update.name')
                    ->action(fn ($builder) => $builder->update(['name' => 'test']))
                    ->allow(false),

                BulkOperation::make('update.description')
                    ->action(fn ($builder) => $builder->update(['name' => 'description'])),

                BulkOperation::make('chunk')
                    ->chunk()
                    ->action(fn ($collection) => $collection->each(fn ($record) => $record->update(['name' => 'chunk']))
                    ),

                BulkOperation::make('chunk.id')
                    ->chunkById()
                    ->action(fn ($collection) => $collection->each(fn ($record) => $record->update(['name' => 'chunk.id']))
                    ),

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
