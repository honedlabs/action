<?php

declare(strict_types=1);

namespace Honed\Action;

use Honed\Core\Concerns\IsDefault;
use Honed\Core\Parameters;

class InlineAction extends Action
{
    use IsDefault;

    /**
     * {@inheritdoc}
     */
    protected $type = 'inline';

    /**
     * {@inheritdoc}
     */
    public function toArray($named = [], $typed = [])
    {
        return \array_merge(parent::toArray($named, $typed), [
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

        [$named, $typed] = Parameters::model($record);

        return $this->evaluate($handler, $named, $typed);
    }
}
