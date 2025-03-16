<?php

declare(strict_types=1);

namespace Honed\Action;

use Honed\Action\Concerns\HasBulkActions;

class PageAction extends Action
{
    use HasBulkActions;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->type(ActionFactory::Page);
    }

    /**
     * {@inheritdoc}
     */
    public function __call($method, $parameters)
    {
        if ($method === 'query') {
            /** @var \Closure(mixed...):void|null $query */
            $query = $parameters[0];

            return $this->queryClosure($query);
        }

        return parent::__call($method, $parameters);
    }
}
