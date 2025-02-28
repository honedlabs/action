<?php

declare(strict_types=1);

namespace Honed\Action\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

trait HasBulkActions
{
    use HasAction;
    use Support\ActsOnRecord;
    use Support\CanChunkById;
    use Support\HasChunkSize;
    use Support\IsChunked;

    /**
     * Execute the bulk action on the given query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>  $builder
     * @return mixed
     */
    public function execute($builder)
    {
        $handler = $this->getHandler();

        if (! $handler) {
            return;
        }

        if ($this->isChunked()) {
            return $this->executeWithChunking($builder, $handler);
        }

        [$named, $typed] = $this->getEvaluationParameters($builder);

        return $this->evaluate($handler, $named, $typed);
    }

    /**
     * Handle the chunking of the records.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>  $builder
     * @param  callable  $handler
     * @return mixed
     */
    protected function executeWithChunking($builder, $handler)
    {
        if ($this->chunksById()) {
            return $builder->chunkById(
                $this->getChunkSize(),
                $this->performChunk($handler)
            );
        }

        return $builder->chunk(
            $this->getChunkSize(),
            $this->performChunk($handler)
        );
    }

    /**
     * Select whether the handler should be called on a record basis, or
     * operates on the collection of records.
     *
     * @param  callable  $handler
     * @return \Closure(Collection<int,\Illuminate\Database\Eloquent\Model>):mixed
     */
    protected function performChunk($handler)
    {
        if ($this->actsOnRecord()) {
            return static fn (Collection $records) => $records
                ->each(static fn ($record) => \call_user_func($handler, $record));
        }

        return static fn (Collection $records) => \call_user_func($handler, $records);
    }

    /**
     * Get the named and typed parameters to use for callable evaluation.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>  $records
     * @return array{array<string, mixed>,  array<class-string, mixed>}
     */
    protected function getEvaluationParameters($records)
    {
        [$model, $singular, $plural] = $this->getParameterNames($records);

        if ($this->actsOnRecord()) {
            $records = $records->get();
        }

        $named = [
            'model' => $records,
            'record' => $records,
            'builder' => $records,
            'query' => $records,
            'records' => $records,
            $singular => $records,
            $plural => $records,
        ];

        $typed = [
            Builder::class => $records,
            EloquentCollection::class => $records,
            Collection::class => $records,
            Model::class => $records,
            $model::class => $records,
        ];

        return [$named, $typed];
    }
}
