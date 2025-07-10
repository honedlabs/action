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
     * Define the batch.
     *
     * @return $this
     */
    protected function definition(): static
    {
        return $this
            ->for(User::class)
            ->inlineOperations([
                InlineOperation::make('show')
                    ->url('users.show', '{user}'),

                InlineOperation::make('inline-name')
                    ->patch()
                    ->rateLimit(1)
                    ->redirect('users.show', '{user}')
                    ->action(fn ($record) => $record->update(['name' => 'test'])),

                InlineOperation::make('inline-description')
                    ->action(fn ($record) => $record->update(['name' => 'description']))
                    ->allow(false),
            ])
            ->bulkOperations([

                BulkOperation::make('bulk-name')
                    ->patch()
                    ->rateLimit(1)
                    ->redirect('/users')
                    ->action(fn ($builder) => $builder->update([
                        'name' => 'test',
                    ])),

                BulkOperation::make('bulk-description')
                    ->action(fn ($builder) => $builder->update([
                        'description' => 'test',
                    ]))
                    ->allow(false),

                BulkOperation::make('chunk')
                    ->chunk()
                    ->action(fn ($collection) => $collection->each(fn ($record) => $record->update([
                        'name' => 'chunk',
                    ]))),

                BulkOperation::make('chunk-id')
                    ->chunkById()
                    ->action(fn ($collection) => $collection->each(fn ($record) => $record->update([
                        'name' => 'chunk.id',
                    ]))),
            ])
            ->pageOperations([

                // Page
                PageOperation::make('create')
                    ->url('users.create'),

                PageOperation::make('create.name')
                    ->post()
                    ->rateLimit(1)
                    ->action(fn () => User::factory()->create([
                        'name' => 'name',
                    ])),

                PageOperation::make('create.description')
                    ->post()
                    ->action(fn () => User::factory()->create([
                        'name' => 'description',
                    ]))
                    ->allow(false),
            ]);
    }
}
