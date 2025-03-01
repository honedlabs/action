<?php

declare(strict_types=1);

namespace Honed\Action;

use Honed\Core\Concerns\IsDefault;
use Illuminate\Database\Eloquent\Model;

class InlineAction extends Action
{
    use IsDefault;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->type(Creator::Inline);
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return \array_merge(parent::toArray(), [
            'default' => $this->isDefault(),
        ]);
    }

    /**
     * Execute the inline action on the given record.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $record
     * @return mixed
     */
    public function execute($record)
    {
        $handler = $this->getHandler();

        if (! $handler) {
            return;
        }

        [$named, $typed] = $this->getEvaluationParameters($record);

        return $this->evaluate($handler, $named, $typed);
    }

    /**
     * Get the named and typed parameters to use for callable evaluation.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $record
     * @return array{array<string, mixed>,  array<class-string, mixed>}
     */
    protected function getEvaluationParameters($record)
    {
        [$model, $singular] = $this->getParameterNames($record);

        $named = [
            'model' => $record,
            'record' => $record,
            $singular => $record,
        ];

        $typed = [
            Model::class => $record,
            $model::class => $record,
        ];

        return [$named, $typed];
    }
}
