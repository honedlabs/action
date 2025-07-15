<?php

declare(strict_types=1);

namespace Honed\Action\Actions;

use Honed\Action\Contracts\FromModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 *
 * @implements \Honed\Action\Contracts\FromModel<TModel>
 */
abstract class DestroyAction extends DatabaseAction implements FromModel
{
    /**
     * Destroy the given ids.
     *
     * @template T of int|string|TModel
     *
     * @param  T|array<int, T>|Collection<int, T>  $ids
     */
    public function handle($ids): void
    {
        $this->transaction(
            fn () => $this->execute($ids)
        );
    }

    /**
     * Execute the action.
     *
     * @template T of int|string|TModel
     *
     * @param  T|array<int, T>|Collection<int, T>  $ids
     */
    protected function execute($ids): void
    {
        if ($ids instanceof Model) {
            $ids = $ids->getKey();
        }

        /** @var int|string $ids */
        $this->from()::destroy($ids);

        $this->after($ids);
    }

    /**
     * Perform additional logic after the action has been executed.
     *
     * @template T of int|string|TModel
     *
     * @param  T|array<int, T>|Collection<int, T>  $ids
     */
    protected function after($ids): void
    {
        //
    }
}
