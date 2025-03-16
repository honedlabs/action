<?php

declare(strict_types=1);

namespace Honed\Action;

use Honed\Core\Concerns\IsDefault;

class InlineAction extends Action
{
    use IsDefault;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->type(ActionFactory::Inline);
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
     * {@inheritdoc}
     */
    public function resolveToArray($parameters = [], $typed = [])
    {
        return \array_merge(parent::resolveToArray($parameters, $typed), [
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

        [$named, $typed] = static::getModelParameters($record);

        return $this->evaluate($handler, $named, $typed);
    }
}
