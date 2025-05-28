<?php

declare(strict_types=1);

namespace Honed\Action\Http\Requests;

use function array_merge;

class DispatchableRequest extends InvokableRequest
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            'id' => ['required'],
        ]);
    }
}
