<?php

declare(strict_types=1);

namespace Honed\Action\Operations;

use Honed\Core\Concerns\IsDefault;
use Honed\Core\Parameters;

class InlineOperation extends Operation
{
    use IsDefault;

    /**
     * Provide the instance with any necessary setup.
     *
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();

        $this->type(self::INLINE);
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return [
            ...parent::toArray(),
            'default' => $this->isDefault(),
        ];
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

    /**
     * Define the inline operation instance.
     *
     * @param  $this  $operation
     * @return $this
     */
    protected function definition(self $operation): self
    {
        return $operation;
    }
}
