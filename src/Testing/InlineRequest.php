<?php

declare(strict_types=1);

namespace Honed\Action\Testing;

use Honed\Action\Support\Constants;

use function array_merge;

class InlineRequest extends FakeRequest
{
    /**
     * The ID of the record.
     *
     * @var string|int|null
     */
    protected $record;

    /**
     * Set the ID of the record.
     *
     * @param  string|int|null  $record
     * @return $this
     */
    public function record($record)
    {
        $this->record = $record;

        return $this;
    }

    /**
     * Get the ID of the record.
     *
     * @return string|int|null
     */
    public function getRecord()
    {
        return $this->record;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return array_merge([
            'type' => Constants::INLINE,
            'record' => $this->getRecord(),
        ], parent::getData());
    }
}
