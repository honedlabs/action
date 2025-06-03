<?php

declare(strict_types=1);

namespace Workbench\App\ActionGroups;

use Honed\Action\ActionGroup;
use Honed\Action\BulkAction;
use Honed\Action\InlineAction;
use Honed\Action\PageAction;
use Workbench\App\Models\User;

/**
 * @template TModel of \Workbench\App\Models\User = \Workbench\App\Models\User
 * @template TBuilder of \Illuminate\Database\Eloquent\Builder<TModel> = \Illuminate\Database\Eloquent\Builder<TModel>
 *
 * @extends \Honed\Action\ActionGroup<TModel, TBuilder>
 */
class UserActions extends ActionGroup
{
    /**
     * Provide the action group with any necessary setup
     *
     * @return void
     */
    public function setUp()
    {
        $this->withResource(User::class);
    }

    /**
     * Define the available actions.
     *
     * @return array<int,\Honed\Action\Action|ActionGroup<TModel, TBuilder>>
     */
    public function actions()
    {
        /** @var array<int,\Honed\Action\Action|ActionGroup<TModel, TBuilder>> */
        return [
            InlineAction::make('show')
                ->route(fn ($user) => route('users.show', $user)),

            InlineAction::make('update.name')
                ->action(fn ($user) => $user->update(['name' => 'test'])),

            InlineAction::make('update.description')
                ->action(fn ($user) => $user->update(['name' => 'description']))
                ->allow(false),

            BulkAction::make('update.name')
                ->action(fn ($user) => $user->update(['name' => 'test']))
                ->allow(false),

            BulkAction::make('update.description')
                ->action(fn ($user) => $user->update(['name' => 'description'])),

            PageAction::make('create')
                ->route('users.create'),

            PageAction::make('create.name')
                ->action(fn () => User::factory()->create([
                    'name' => 'name',
                ])),

            PageAction::make('create.description')
                ->action(fn () => User::factory()->create([
                    'name' => 'description',
                ]))
                ->allow(false),
        ];
    }
}
